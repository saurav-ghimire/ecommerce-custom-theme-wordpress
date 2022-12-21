class Modal
{
    constructor() {
        this.triggers = document.querySelectorAll('[data-modal]');
        this.clearButtons = document.querySelectorAll('.ag-media-clear');
        this.listeners();
    }

    listeners = () => {
        this.triggers.forEach(el => {
            el.addEventListener('click', this.openModal, false);
        });

        this.clearButtons.forEach(el => {
            el.addEventListener('click', this.clear, false);
        });
    }

    clear = (e) => {
        const root = e.currentTarget.parentNode;
        root.querySelector('input').value = '';
        root.querySelector('.ag-preview').innerHTML = '';
    }

    openModal = (e) => {
        let file_frame = void 0;

        const root = e.currentTarget.parentNode;
        const types = root.querySelector('input').name.match(/logo/) ? ['image'] : ['image', 'video'];
        const selected = root.querySelector('input').value;
        const preview = root.querySelector('.ag-preview');

        if (typeof wp !== 'undefined' && wp.media && wp.media.editor) {
            if (file_frame) {
                file_frame.uploader.param('post_id', selected);
                file_frame.open();
                return;
            }
        }



        file_frame = wp.media.frames.select_image = wp.media({
            title: 'Select image',
            button: {
                text: 'Add image',
            },
            library: {
                type: types,
            },
            multiple: false,
            post_id: selected,
        });

        wp.media.model.settings.post.id = parseInt(selected);

        console.log(wp.media.model.settings.post);

        file_frame.on('select', () => {
            const image = file_frame.state().get('selection').first().toJSON();

            const {
                url,
                id,
                mime,
            } = image;

            preview.innerHTML = '';
            root.querySelector('input').value = id;
            const img = document.createElement(mime.match(/video/) ? 'video' : 'img');

            img.src = url;

            preview.appendChild(img);
        });

        file_frame.open();
    }
}

export default Modal;
