/**
 * Inicializa la gestión de colores/stock/imágenes dentro del editor de producto.
 */
window.YofiProductEditor = (function () {
    function postJson(url, body) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': window.YOFI_ADMIN.csrfToken
            },
            body: JSON.stringify(body)
        }).then(function (r) { return r.json(); });
    }

    function collectStocks(container) {
        var stocks = {};
        container.querySelectorAll('.stock-input').forEach(function (input) {
            stocks[input.dataset.idTalle] = parseInt(input.value, 10) || 0;
        });
        return stocks;
    }

    function bindColorSwatches(root) {
        root.querySelectorAll('.color-swatch-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id = btn.getAttribute('data-color-panel');
                root.querySelectorAll('.color-swatch-btn').forEach(function (b) { b.classList.remove('active'); });
                root.querySelectorAll('.color-panel').forEach(function (p) { p.classList.remove('active'); });
                btn.classList.add('active');
                var panel = root.querySelector('#colorPanel' + id);
                if (panel) panel.classList.add('active');
            });
        });
    }

    function bindStockButtons(root, idProd) {
        root.querySelectorAll('.btn-guardar-stock').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var idColor = parseInt(btn.dataset.idColor, 10);
                var panel = btn.closest('.color-panel');
                window.YofiAdmin.setButtonLoading(btn, true, 'Guardando...');
                postJson(window.YOFI_ADMIN.basePath + '/admin/api/guardar-stock-color.php', {
                    id_prod: idProd,
                    id_color: idColor,
                    stocks: collectStocks(panel)
                }).then(function (data) {
                    window.YofiAdmin.setButtonLoading(btn, false);
                    if (data.success) {
                        btn.textContent = 'Guardado ✓';
                        setTimeout(function () { btn.textContent = 'Guardar stock'; }, 2000);
                    } else {
                        alert(data.error || 'Error al guardar stock');
                    }
                });
            });
        });
    }

    function bindImageButtons(root, idProd) {
        root.querySelectorAll('.btn-eliminar-imagen').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (!confirm('¿Eliminar esta imagen?')) return;
                window.YofiAdmin.setButtonLoading(btn, true, 'Eliminando...');
                postJson(window.YOFI_ADMIN.basePath + '/admin/api/eliminar-imagen-producto.php', {
                    id_imagen: parseInt(btn.dataset.idImagen, 10),
                    id_prod: idProd
                }).then(function (data) {
                    if (data.success) {
                        if (window.YofiProductDrawer && window.YofiProductDrawer.reload) {
                            window.YofiProductDrawer.reload(idProd);
                        } else {
                            location.reload();
                        }
                    } else {
                        window.YofiAdmin.setButtonLoading(btn, false);
                        alert(data.error || 'No se pudo eliminar');
                    }
                });
            });
        });

        root.querySelectorAll('.btn-principal').forEach(function (btn) {
            btn.addEventListener('click', function () {
                window.YofiAdmin.setButtonLoading(btn, true, 'Actualizando...');
                postJson(window.YOFI_ADMIN.basePath + '/admin/api/marcar-imagen-principal.php', {
                    id_imagen: parseInt(btn.dataset.idImagen, 10),
                    id_prod: idProd
                }).then(function (data) {
                    if (data.success) {
                        if (window.YofiProductDrawer && window.YofiProductDrawer.reload) {
                            window.YofiProductDrawer.reload(idProd);
                        } else {
                            location.reload();
                        }
                    } else {
                        window.YofiAdmin.setButtonLoading(btn, false);
                    }
                });
            });
        });
    }

    function bindNewColor(root, idProd) {
        var toggleBtn = root.querySelector('#btnToggleNuevoColor');
        var panel = root.querySelector('#panelNuevoColor');
        if (toggleBtn && panel) {
            toggleBtn.addEventListener('click', function () {
                panel.classList.toggle('d-none');
            });
        }

        var saveBtn = root.querySelector('.btn-guardar-color');
        if (!saveBtn) return;

        saveBtn.addEventListener('click', function () {
            var form = saveBtn.closest('.color-variant-form');
            var select = form.querySelector('.color-select');
            var idColor = parseInt(select.value, 10);
            if (!idColor) {
                alert('Seleccioná un color');
                return;
            }
            window.YofiAdmin.setButtonLoading(saveBtn, true, 'Guardando...');
            postJson(window.YOFI_ADMIN.basePath + '/admin/api/guardar-stock-color.php', {
                id_prod: idProd,
                id_color: idColor,
                stocks: collectStocks(form)
            }).then(function (data) {
                if (!data.success) {
                    window.YofiAdmin.setButtonLoading(saveBtn, false);
                    alert(data.error || 'Error al guardar stock');
                    return;
                }
                var fileInput = form.querySelector('input[type="file"]');
                if (fileInput && fileInput.files.length > 0) {
                    form.submit();
                } else if (window.YofiProductDrawer && window.YofiProductDrawer.reload) {
                    window.YofiProductDrawer.reload(idProd);
                } else {
                    location.reload();
                }
            });
        });
    }

    function init(root) {
        if (!root) return;
        var section = root.querySelector('#product-colors-section');
        if (!section) return;
        var idProd = parseInt(section.dataset.idProd, 10);
        if (!idProd) return;
        bindColorSwatches(root);
        bindStockButtons(root, idProd);
        bindImageButtons(root, idProd);
        bindNewColor(root, idProd);
    }

    return { init: init };
})();
