import { Canvas, Rect, Ellipse, Line, Triangle, Textbox, Group, FabricImage, PencilBrush, Point } from 'fabric';

const SHAPE_TOOLS = ['line', 'arrow', 'rectangle', 'ellipse'];
const STROKE_COLOR = '#1e293b';
const AUTOSAVE_DELAY_MS = 1500;

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

function initDrawingEditor(root) {
    if (root.dataset.initialized === '1') {
        return;
    }
    root.dataset.initialized = '1';

    const canvasEl = root.querySelector('[data-canvas]');
    const container = root.querySelector('[data-canvas-container]');
    const jsonEl = root.querySelector('script[data-canvas-json]');
    const statusEl = root.querySelector('[data-save-status]');
    const zoomLevelEl = root.querySelector('[data-zoom-level]');
    const imageInput = root.querySelector('[data-image-input]');

    const canEdit = root.dataset.canEdit === '1';
    const canExport = root.dataset.canExport === '1';
    const saveUrl = root.dataset.saveUrl;
    const imageUrl = root.dataset.imageUrl;
    const exportPdfUrl = root.dataset.exportPdfUrl;
    const drawingTitle = root.dataset.drawingTitle || 'drawing';

    const canvas = new Canvas(canvasEl, {
        backgroundColor: '#ffffff',
        selection: canEdit,
        preserveObjectStacking: true,
    });

    function resize() {
        const rect = container.getBoundingClientRect();
        canvas.setDimensions({ width: Math.max(rect.width, 1), height: Math.max(rect.height, 1) });
        canvas.renderAll();
    }
    new ResizeObserver(resize).observe(container);
    resize();

    let initialData = {};
    try {
        initialData = JSON.parse(jsonEl?.textContent || '{}');
    } catch {
        initialData = {};
    }

    const hasObjects = Array.isArray(initialData?.objects) && initialData.objects.length > 0;
    const ready = hasObjects
        ? canvas.loadFromJSON(initialData).then(() => canvas.renderAll())
        : Promise.resolve();

    if (!canEdit) {
        canvas.selection = false;
    }

    // ==================== History (undo/redo) ====================
    let undoStack = [];
    let redoStack = [];
    let historyLocked = false;
    let historySeeded = false;

    function pushHistory() {
        if (historyLocked || !historySeeded) {
            return;
        }
        undoStack.push(JSON.stringify(canvas.toJSON()));
        if (undoStack.length > 50) {
            undoStack.shift();
        }
        redoStack = [];
    }

    function restoreState(state) {
        historyLocked = true;
        canvas.loadFromJSON(JSON.parse(state)).then(() => {
            canvas.renderAll();
            historyLocked = false;
        });
    }

    function undo() {
        if (undoStack.length < 2) {
            return;
        }
        redoStack.push(undoStack.pop());
        restoreState(undoStack[undoStack.length - 1]);
        scheduleAutosave();
    }

    function redo() {
        if (!redoStack.length) {
            return;
        }
        const state = redoStack.pop();
        undoStack.push(state);
        restoreState(state);
        scheduleAutosave();
    }

    ready.then(() => {
        undoStack = [JSON.stringify(canvas.toJSON())];
        historySeeded = true;
    });

    // ==================== Autosave ====================
    let autosaveTimer = null;
    let lastSavedAt = null;
    let tickTimer = null;

    function setStatus(text) {
        if (statusEl) {
            statusEl.textContent = text;
        }
    }

    function tickSavedAgo() {
        if (!lastSavedAt) {
            return;
        }
        const seconds = Math.max(0, Math.round((Date.now() - lastSavedAt) / 1000));
        setStatus(seconds < 3 ? 'Saved' : `Last saved ${seconds}s ago`);
    }

    function persist(includeThumbnail) {
        if (!canEdit) {
            return Promise.resolve();
        }

        const payload = { canvas_data: canvas.toJSON() };
        if (includeThumbnail) {
            payload.thumbnail = canvas.toDataURL({ format: 'png', multiplier: Math.min(1, 400 / canvas.getWidth()) });
        }

        setStatus('Saving…');

        return fetch(saveUrl, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                Accept: 'application/json',
            },
            body: JSON.stringify(payload),
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('save failed');
                }
                lastSavedAt = Date.now();
                setStatus('Saved');
            })
            .catch(() => setStatus('Could not save — check your connection'));
    }

    function scheduleAutosave() {
        if (!canEdit) {
            return;
        }
        setStatus('Editing…');
        clearTimeout(autosaveTimer);
        autosaveTimer = setTimeout(() => persist(false), AUTOSAVE_DELAY_MS);
    }

    if (canEdit) {
        tickTimer = setInterval(tickSavedAgo, 5000);
    }

    canvas.on('object:added', () => {
        pushHistory();
        if (historySeeded) {
            scheduleAutosave();
        }
    });
    canvas.on('object:modified', () => {
        pushHistory();
        scheduleAutosave();
    });
    canvas.on('object:removed', () => {
        pushHistory();
        if (historySeeded) {
            scheduleAutosave();
        }
    });
    canvas.on('path:created', () => scheduleAutosave());

    // ==================== Tools ====================
    let activeTool = 'select';
    let isPanning = false;
    let panLast = null;
    let drawingShape = null;
    let shapeStart = null;

    function setActiveTool(tool) {
        activeTool = tool;
        root.querySelectorAll('[data-tool]').forEach((btn) => {
            btn.dataset.active = String(btn.dataset.tool === tool);
        });

        canvas.isDrawingMode = false;
        canvas.selection = tool === 'select';
        canvas.forEachObject((o) => {
            o.selectable = tool === 'select';
            o.evented = tool === 'select' || tool === 'eraser';
        });
        canvas.defaultCursor = tool === 'hand' ? 'grab' : tool === 'select' ? 'default' : 'crosshair';

        if (tool === 'pen' || tool === 'highlighter') {
            canvas.isDrawingMode = true;
            const brush = new PencilBrush(canvas);
            if (tool === 'pen') {
                brush.width = 3;
                brush.color = STROKE_COLOR;
            } else {
                brush.width = 18;
                brush.color = 'rgba(250, 204, 21, 0.45)';
            }
            canvas.freeDrawingBrush = brush;
        }
    }

    function addText(point) {
        const textbox = new Textbox('Double-click to edit', {
            left: point.x,
            top: point.y,
            width: 200,
            fontSize: 18,
            fill: STROKE_COLOR,
            fontFamily: 'sans-serif',
        });
        canvas.add(textbox);
        canvas.setActiveObject(textbox);
    }

    function addSticky(point) {
        const rect = new Rect({
            left: 0,
            top: 0,
            width: 180,
            height: 130,
            fill: '#fef08a',
            rx: 6,
            ry: 6,
            shadow: { color: 'rgba(0,0,0,0.15)', blur: 6, offsetY: 2 },
        });
        const text = new Textbox('Note', {
            left: 10,
            top: 10,
            width: 160,
            fontSize: 14,
            fill: '#713f12',
            fontFamily: 'sans-serif',
        });
        const group = new Group([rect, text], { left: point.x, top: point.y });
        canvas.add(group);
        canvas.setActiveObject(group);
    }

    function createPreviewShape(tool, p) {
        if (tool === 'rectangle') {
            return new Rect({ left: p.x, top: p.y, width: 1, height: 1, stroke: STROKE_COLOR, strokeWidth: 2, fill: 'transparent' });
        }
        if (tool === 'ellipse') {
            return new Ellipse({ left: p.x, top: p.y, rx: 1, ry: 1, stroke: STROKE_COLOR, strokeWidth: 2, fill: 'transparent' });
        }
        if (tool === 'line' || tool === 'arrow') {
            return new Line([p.x, p.y, p.x, p.y], { stroke: STROKE_COLOR, strokeWidth: 2 });
        }
        return null;
    }

    function updatePreviewShape(shape, tool, start, current) {
        if (tool === 'rectangle') {
            shape.set({
                left: Math.min(start.x, current.x),
                top: Math.min(start.y, current.y),
                width: Math.abs(current.x - start.x),
                height: Math.abs(current.y - start.y),
            });
        } else if (tool === 'ellipse') {
            shape.set({
                left: Math.min(start.x, current.x),
                top: Math.min(start.y, current.y),
                rx: Math.abs(current.x - start.x) / 2,
                ry: Math.abs(current.y - start.y) / 2,
            });
        } else if (tool === 'line' || tool === 'arrow') {
            shape.set({ x2: current.x, y2: current.y });
        }
        shape.setCoords();
    }

    function finalizeArrow(start, end) {
        const line = new Line([start.x, start.y, end.x, end.y], { stroke: STROKE_COLOR, strokeWidth: 2 });
        const angle = (Math.atan2(end.y - start.y, end.x - start.x) * 180) / Math.PI + 90;
        const head = new Triangle({
            left: end.x,
            top: end.y,
            width: 14,
            height: 16,
            fill: STROKE_COLOR,
            angle,
            originX: 'center',
            originY: 'center',
        });
        return new Group([line, head]);
    }

    root.querySelectorAll('[data-tool]').forEach((btn) => {
        btn.addEventListener('click', () => setActiveTool(btn.dataset.tool));
    });

    canvas.on('mouse:down', (opt) => {
        if (!canEdit) {
            return;
        }

        if (activeTool === 'eraser') {
            if (opt.target) {
                canvas.remove(opt.target);
            }
            return;
        }

        if (activeTool === 'hand') {
            isPanning = true;
            panLast = { x: opt.e.clientX, y: opt.e.clientY };
            return;
        }

        if (SHAPE_TOOLS.includes(activeTool)) {
            const p = canvas.getScenePoint(opt.e);
            shapeStart = p;
            if (activeTool !== 'arrow') {
                drawingShape = createPreviewShape(activeTool, p);
                canvas.add(drawingShape);
            } else {
                drawingShape = createPreviewShape('line', p);
                canvas.add(drawingShape);
            }
            return;
        }

        if (activeTool === 'text') {
            addText(canvas.getScenePoint(opt.e));
            setActiveTool('select');
            return;
        }

        if (activeTool === 'sticky') {
            addSticky(canvas.getScenePoint(opt.e));
            setActiveTool('select');
        }
    });

    canvas.on('mouse:move', (opt) => {
        if (isPanning && panLast) {
            const vpt = canvas.viewportTransform;
            vpt[4] += opt.e.clientX - panLast.x;
            vpt[5] += opt.e.clientY - panLast.y;
            canvas.requestRenderAll();
            panLast = { x: opt.e.clientX, y: opt.e.clientY };
        } else if (drawingShape && shapeStart) {
            const tool = activeTool === 'arrow' ? 'line' : activeTool;
            updatePreviewShape(drawingShape, tool, shapeStart, canvas.getScenePoint(opt.e));
            canvas.requestRenderAll();
        }
    });

    canvas.on('mouse:up', (opt) => {
        isPanning = false;
        panLast = null;

        if (drawingShape && shapeStart) {
            const end = canvas.getScenePoint(opt.e);

            if (activeTool === 'arrow') {
                canvas.remove(drawingShape);
                canvas.add(finalizeArrow(shapeStart, end));
            } else {
                drawingShape.setCoords();
            }

            drawingShape = null;
            shapeStart = null;
            setActiveTool('select');
        }
    });

    // Zoom with Ctrl/Cmd + wheel
    canvas.on('mouse:wheel', (opt) => {
        if (!opt.e.ctrlKey && !opt.e.metaKey) {
            return;
        }
        opt.e.preventDefault();
        opt.e.stopPropagation();
        const delta = opt.e.deltaY;
        let zoom = canvas.getZoom() * 0.999 ** delta;
        zoom = Math.min(Math.max(zoom, 0.1), 5);
        canvas.zoomToPoint(new Point(opt.e.offsetX, opt.e.offsetY), zoom);
        updateZoomLabel();
    });

    function updateZoomLabel() {
        if (zoomLevelEl) {
            zoomLevelEl.textContent = `${Math.round(canvas.getZoom() * 100)}%`;
        }
    }

    function zoomBy(factor) {
        const zoom = Math.min(Math.max(canvas.getZoom() * factor, 0.1), 5);
        canvas.zoomToPoint(new Point(canvas.getWidth() / 2, canvas.getHeight() / 2), zoom);
        updateZoomLabel();
    }

    function zoomToFit() {
        canvas.setViewportTransform([1, 0, 0, 1, 0, 0]);
        const objects = canvas.getObjects();
        if (!objects.length) {
            updateZoomLabel();
            return;
        }
        const bounds = objects.reduce((acc, o) => {
            const r = o.getBoundingRect();
            return {
                left: Math.min(acc.left, r.left),
                top: Math.min(acc.top, r.top),
                right: Math.max(acc.right, r.left + r.width),
                bottom: Math.max(acc.bottom, r.top + r.height),
            };
        }, { left: Infinity, top: Infinity, right: -Infinity, bottom: -Infinity });

        const width = bounds.right - bounds.left || 1;
        const height = bounds.bottom - bounds.top || 1;
        const zoom = Math.min(canvas.getWidth() / (width + 80), canvas.getHeight() / (height + 80), 2);

        canvas.setZoom(zoom);
        canvas.absolutePan(new Point(bounds.left * zoom - 40, bounds.top * zoom - 40));
        updateZoomLabel();
    }

    // ==================== Clipboard / duplicate / delete ====================
    let clipboard = null;

    function deleteSelection() {
        const active = canvas.getActiveObjects();
        if (!active.length) {
            return;
        }
        active.forEach((obj) => canvas.remove(obj));
        canvas.discardActiveObject();
        canvas.requestRenderAll();
    }

    function duplicateSelection() {
        const active = canvas.getActiveObject();
        if (!active) {
            return;
        }
        active.clone().then((cloned) => {
            cloned.set({ left: (active.left ?? 0) + 16, top: (active.top ?? 0) + 16 });
            canvas.add(cloned);
            canvas.setActiveObject(cloned);
            canvas.requestRenderAll();
        });
    }

    function copySelection() {
        const active = canvas.getActiveObject();
        if (!active) {
            return;
        }
        active.clone().then((cloned) => {
            clipboard = cloned;
        });
    }

    function pasteClipboard() {
        if (!clipboard) {
            return;
        }
        clipboard.clone().then((cloned) => {
            cloned.set({ left: (clipboard.left ?? 0) + 16, top: (clipboard.top ?? 0) + 16 });
            canvas.add(cloned);
            canvas.setActiveObject(cloned);
            canvas.requestRenderAll();
        });
    }

    // ==================== Image upload ====================
    function uploadImage(file) {
        const form = new FormData();
        form.append('image', file);

        setStatus('Uploading image…');

        fetch(imageUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken(), Accept: 'application/json' },
            body: form,
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('upload failed');
                }
                return response.json();
            })
            .then(({ url }) => FabricImage.fromURL(url, { crossOrigin: 'anonymous' }))
            .then((img) => {
                img.scaleToWidth(Math.min(320, canvas.getWidth() * 0.5));
                img.set({ left: 40, top: 40 });
                canvas.add(img);
                canvas.setActiveObject(img);
                setStatus('Saved');
            })
            .catch(() => setStatus('Image upload failed'));
    }

    imageInput?.addEventListener('change', (event) => {
        const file = event.target.files?.[0];
        if (file) {
            uploadImage(file);
        }
        event.target.value = '';
    });

    // ==================== Export ====================
    function exportPng() {
        const dataUrl = canvas.toDataURL({ format: 'png', multiplier: 2 });
        const link = document.createElement('a');
        link.href = dataUrl;
        link.download = `${drawingTitle}.png`;
        document.body.appendChild(link);
        link.click();
        link.remove();
    }

    function exportPdf() {
        setStatus('Preparing PDF…');
        const dataUrl = canvas.toDataURL({ format: 'png', multiplier: 2 });

        fetch(exportPdfUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
            body: JSON.stringify({ image: dataUrl }),
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('export failed');
                }
                return response.blob();
            })
            .then((blob) => {
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = `${drawingTitle}.pdf`;
                document.body.appendChild(link);
                link.click();
                link.remove();
                URL.revokeObjectURL(url);
                setStatus('Saved');
            })
            .catch(() => setStatus('PDF export failed'));
    }

    // ==================== Toolbar actions ====================
    root.querySelectorAll('[data-action]').forEach((btn) => {
        btn.addEventListener('click', () => {
            switch (btn.dataset.action) {
                case 'undo': return undo();
                case 'redo': return redo();
                case 'delete': return deleteSelection();
                case 'duplicate': return duplicateSelection();
                case 'save': return persist(true);
                case 'zoom-in': return zoomBy(1.2);
                case 'zoom-out': return zoomBy(1 / 1.2);
                case 'zoom-fit': return zoomToFit();
                case 'image': return imageInput?.click();
                case 'export-png': return canExport ? exportPng() : undefined;
                case 'export-pdf': return canExport ? exportPdf() : undefined;
                default: return undefined;
            }
        });
    });

    // ==================== Keyboard shortcuts ====================
    document.addEventListener('keydown', (event) => {
        if (!root.isConnected || !canEdit) {
            return;
        }
        const editingText = canvas.getActiveObject()?.isEditing;
        const mod = event.ctrlKey || event.metaKey;

        if (mod && event.key.toLowerCase() === 'z' && event.shiftKey) {
            event.preventDefault();
            redo();
        } else if (mod && event.key.toLowerCase() === 'z') {
            event.preventDefault();
            undo();
        } else if (mod && event.key.toLowerCase() === 's') {
            event.preventDefault();
            persist(true);
        } else if (mod && event.key.toLowerCase() === 'd' && !editingText) {
            event.preventDefault();
            duplicateSelection();
        } else if (mod && event.key.toLowerCase() === 'c' && !editingText) {
            copySelection();
        } else if (mod && event.key.toLowerCase() === 'v' && !editingText) {
            pasteClipboard();
        } else if ((event.key === 'Delete' || event.key === 'Backspace') && !editingText && document.activeElement === document.body) {
            event.preventDefault();
            deleteSelection();
        } else if (event.key === 'Escape') {
            setActiveTool('select');
        }
    });

    updateZoomLabel();
}

function scanForDrawingEditors() {
    document.querySelectorAll('[data-drawing-editor]').forEach(initDrawingEditor);
}

document.addEventListener('DOMContentLoaded', scanForDrawingEditors);
document.addEventListener('livewire:navigated', scanForDrawingEditors);
