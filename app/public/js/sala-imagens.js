(function () {
    var selector = document.getElementById('seletor-imagem-sala');
    if (!selector) return;

    var overlay = document.getElementById('overlayImagens');
    var grid = document.getElementById('gridImagens');
    var loadMoreButton = document.getElementById('btnCarregarMais');
    var imageInput = document.getElementById('newimagem');
    var imageIdInput = document.getElementById('imagem_id');
    var preview = document.getElementById('imagem');
    var placeholder = document.getElementById('imagemPlaceholder');
    var errorMessage = document.getElementById('erroImagens');
    var page = 0;
    var hasMore = true;
    var loading = false;

    document.body.appendChild(overlay);

    function showError(error) {
        errorMessage.textContent = error;
        errorMessage.style.display = 'block';
    }

    function clearError() {
        errorMessage.textContent = '';
        errorMessage.style.display = 'none';
    }

    function closeOverlay() {
        overlay.style.display = 'none';
    }

    function setPreview(source) {
        preview.src = source;
        preview.style.display = 'block';
        if (placeholder) placeholder.style.display = 'none';
    }

    function openOverlay() {
        overlay.style.display = 'block';
        if (grid.children.length === 0) loadMore();
    }

    function selectImage(image) {
        imageIdInput.value = image.dataset.id;
        setPreview('data:image/jpeg;base64,' + image.dataset.base64);
        closeOverlay();
    }

    function deleteImage(imageId) {
        var params = new URLSearchParams();
        params.append('id', selector.dataset.salaId);
        params.append('categoria_filtro', selector.dataset.categoryFilter);
        params.append('activas', selector.dataset.activeFilter);
        params.append('csrf_token', selector.dataset.csrfToken);
        params.append('eliminar_imagem', '1');
        params.append('imagem_id', imageId);

        fetch(selector.dataset.saveUrl, {
            method: 'POST',
            body: params,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function (response) {
            if (!response.ok) throw new Error('Não foi possível eliminar a imagem.');
            window.location.reload();
        }).catch(function () {
            showError('Não foi possível eliminar a imagem. Tente novamente.');
        });
    }

    function appendImage(image) {
        var thumb = document.createElement('div');
        thumb.className = 'img-thumb';
        thumb.dataset.id = image.id;
        thumb.dataset.base64 = image.base64;
        thumb.style.cssText = 'position:relative; cursor:pointer; border:1px solid #ccc; border-radius:4px; overflow:hidden; text-align:center;';

        var imageElement = document.createElement('img');
        imageElement.src = 'data:image/jpeg;base64,' + image.base64;
        imageElement.alt = image.nome;
        imageElement.style.cssText = 'width:100%; height:60px; object-fit:cover;';
        thumb.appendChild(imageElement);

        var label = document.createElement('div');
        label.style.cssText = 'font-size:9px; padding:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;';
        label.textContent = image.nome;
        thumb.appendChild(label);

        var deleteButton = document.createElement('button');
        deleteButton.type = 'button';
        deleteButton.className = 'eliminar-imagem';
        deleteButton.style.cssText = 'position:absolute; top:2px; right:2px; background:rgba(128,128,128,0.7); color:#fff; border:none; border-radius:50%; width:1.14rem; height:1.14rem; font-size:0.71rem; line-height:1rem; padding:0; cursor:pointer; z-index:10;';
        deleteButton.title = 'Eliminar imagem';
        deleteButton.setAttribute('aria-label', 'Eliminar imagem ' + image.nome);
        deleteButton.textContent = '×';
        thumb.appendChild(deleteButton);

        grid.appendChild(thumb);
    }

    function loadMore() {
        if (loading || !hasMore) return;
        loading = true;
        clearError();
        page++;

        fetch(selector.dataset.listUrl + '&page=' + page).then(function (response) {
            if (!response.ok) throw new Error('Não foi possível carregar as imagens.');
            return response.json();
        }).then(function (data) {
            (data.images || []).forEach(appendImage);
            hasMore = !!data.hasMore;
            loadMoreButton.style.display = hasMore ? 'block' : 'none';
        }).catch(function () {
            page--;
            showError('Não foi possível carregar as imagens. Tente novamente.');
        }).then(function () {
            loading = false;
        });
    }

    selector.addEventListener('click', function (event) {
        if (event.target === preview || event.target === placeholder) {
            openOverlay();
        }
    });

    overlay.addEventListener('click', function (event) {
        if (event.target === overlay || event.target.closest('.fechar-overlay-imagens')) {
            closeOverlay();
        } else if (event.target.id === 'btnCarregar') {
            closeOverlay();
            imageInput.click();
        } else if (event.target.classList.contains('sem-imagem')) {
            imageIdInput.value = '';
            preview.style.display = 'none';
            if (placeholder) placeholder.style.display = 'flex';
            closeOverlay();
        } else if (event.target.closest('.eliminar-imagem')) {
            event.stopPropagation();
            deleteImage(event.target.closest('.img-thumb').dataset.id);
        } else {
            var thumb = event.target.closest('.img-thumb');
            if (thumb) selectImage(thumb);
        }
    });

    loadMoreButton.addEventListener('click', loadMore);

    imageInput.addEventListener('change', function () {
        if (!imageInput.files || !imageInput.files[0]) return;

        var reader = new FileReader();
        reader.onload = function (event) {
            setPreview(event.target.result);
            imageIdInput.value = '';
        };
        reader.onerror = function () {
            showError('Não foi possível pré-visualizar a imagem selecionada.');
        };
        reader.readAsDataURL(imageInput.files[0]);
    });
})();
