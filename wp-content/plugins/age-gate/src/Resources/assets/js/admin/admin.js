import Modal from "./modules/wp-picker";
import { LinkPicker } from './components/LinkPicker';
import SimpleMDE from 'simplemde/dist/simplemde.min.js';
import hljs from 'highlight.js/lib/core';
import css from 'highlight.js/lib/languages/css';
import Alpine from 'alpinejs'
import { accordion } from "./components/Accordion";
// import './components/GreedyNav';
// import Bouncer from 'formbouncerjs';


window.addEventListener('DOMContentLoaded', () => {
    accordion();

    // const validate = new Bouncer('.ag-settings-form');

    hljs.registerLanguage('css', css);
    document.querySelectorAll('.language-css').forEach(el => {
        // then highlight each
        hljs.highlightElement(el);
    });

    window.Alpine = Alpine
    Alpine.start();

    new Modal();
    new LinkPicker;

    document.querySelectorAll('[name="ag_settings[all]"]').forEach(toggle => {
        toggle.addEventListener('change', (e) => {
            toggle.closest('table').querySelectorAll('[type="checkbox"]').forEach(input => input.checked = toggle.checked);
        });
    });

    document.querySelectorAll('.ag-file').forEach(file => {
        file.addEventListener('change', (e) => file.parentNode.querySelector('.ag-file-custom').dataset.text = e.target.files[0].name )
    });

    //
    const paramsString = window.location.search;

    // console.log(paramsString);
    const searchParams = new URLSearchParams(paramsString);

    searchParams.delete('m');

    console.log(searchParams.toString())
    history.replaceState(null, '', `admin.php?${searchParams.toString() }`);

    Array.from(document.querySelectorAll('.ag-rte')).forEach((editor) => {
        const simplemde = new SimpleMDE({
            element: editor,
            spellChecker: false,
            forceSync: true,
        });
    });

    // Removed as not allowed
    // Array.from(document.querySelectorAll('.ag-css')).forEach((editor) => {
    //     wp.codeEditor.initialize(editor, {});
    // });

    Array.from(document.querySelectorAll('.ag-form__reset')).forEach((frm) => {

        // frm.addEventListener('submit', (e) => (!confirm('Are you sure?') ? e.preventDefault() : true) );
        // async (e) => {
        //     return confirm('Are you sure?') === true;
        //     // return await Swal.fire({
        //     //     title: 'Are you sure?',
        //     //     text: "You won't be able to revert this!",
        //     //     icon: 'warning',
        //     //     showCancelButton: true,
        //     //     confirmButtonColor: '#3085d6',
        //     //     cancelButtonColor: '#d33',
        //     //     confirmButtonText: 'Yes, delete it!'
        //     // }).then((result) => {
        //     //     return result.isConfirmed


        //     // })
        // });
    });

    Array.from(document.querySelectorAll('.ag-remove-legacy-css')).forEach((button) => {
        console.log(button);
        button.addEventListener('click', (e) => {
            if (confirm('Are you sure?')) {
                const { ajaxurl } = window;

                const data = new FormData;
                data.append('action', 'ag_clear_legacy_css');
                data.append('nonce', e.currentTarget.dataset.id);

                fetch(ajaxurl, {
                    method: 'POST',
                    body: data,
                })

                .then(r => {
                    const { status } = r;

                    if (status !== 200) {
                        throw new Error(status)
                    }

                    return r.json()
                })
                .then(data => {
                    window.location.reload();
                })
                .catch(resp => {
                    alert('Failed to execute request');
                });

            }
        });
    });
});
