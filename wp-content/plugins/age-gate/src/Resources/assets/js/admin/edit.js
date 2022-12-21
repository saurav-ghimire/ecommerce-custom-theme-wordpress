import { fireEvent } from "../public/utility";

if (!String.format) {
    String.format = function (format) {
        var args = Array.prototype.slice.call(arguments, 1);
        return format.replace(/{(\d+)}/g, function (match, number) {
            return typeof args[number] != 'undefined'
                ? args[number]
                : match
                ;
        });
    };
}

window.addEventListener('DOMContentLoaded', () => {

    const title = document.querySelector('.ag-post-metabox__title');
    const icon = title ? title.querySelector('i') : document.createElement('i');
    const text = title ? title.querySelector('span') : document.createElement('span');
    const button = title ? title.querySelector('button') : document.createElement('button');

    document.querySelectorAll('.ag-post-metabox__age-toggle').forEach(button => {
        button.addEventListener('click', () => document.body.classList.toggle('ag-show-age'));
    });

    document.querySelectorAll('.ag-post-metabox__set').forEach(button => {
        button.addEventListener('click', (e) => {
            if (title) {
                title.dataset.age = e.currentTarget.parentNode.querySelector('input').value;

                const {
                    dataset: {
                        age,
                        textRestrict,
                    }
                } = title;

                text.textContent = String.format(textRestrict, age);
                fireEvent('AgeGateSetAge', age);
            }


            document.body.classList.toggle('ag-show-age');


        });
    });

    document.querySelectorAll('.ag_settings_switch').forEach(toggle => {
        toggle.addEventListener('change', (e) => {

            if (!title) {
                return;
            }

            const checked = e.target.checked;

            const {
                dataset: {
                    age,
                    textRestrict,
                    textUnrestrict,
                }
            } = title;

            // TODO: DRY!!!!!
            if (e.target.name.match(/bypass/)) {
                console.log('bypass');
                if (checked) {
                    if (button) {
                        button.style.display = 'none';
                    }

                    document.body.classList.remove('ag-show-age');
                    icon.className = 'dashicons dashicons-unlock';
                    text.textContent = textUnrestrict;

                } else {
                    if (button) {
                        button.style.display = 'inline-block';
                    }

                    icon.className = 'dashicons dashicons-lock';
                    text.textContent = String.format(textRestrict, age);
                }
            } else {
                console.log('no match');
                if (checked) {
                    if (button) {
                        button.style.display = 'inline-block';
                    }

                    icon.className = 'dashicons dashicons-lock';
                    text.textContent = String.format(textRestrict, age);
                } else {
                    if (button) {
                        button.style.display = 'none';
                    }

                    document.body.classList.remove('ag-show-age');
                    icon.className = 'dashicons dashicons-unlock';
                    text.textContent = textUnrestrict;
                }
            }

        });
    });

    // handle language change dropdowns
    const languageSelector = document.querySelector('[name="post_lang_choice"], [name="icl_post_language"], [name="term_lang_choice"], [name="icl_tax_category_language"]');

    console.log(languageSelector);
    window.addEventListener('AgeGateSetAge', (e) => {
        console.log(e);
    })

    if (languageSelector) {
        const { agagemap } = window;

        const initial = languageSelector.value;
        const input = document.querySelector('[name="ag_settings[age]"]');

        console.log(initial, agagemap);
        // if no input its not multi-age
        if (input) {
            const isCustom = agagemap[initial] != input.value;

            console.log(agagemap[initial], input.value)
            console.log(isCustom);

            languageSelector.addEventListener('change', (e) => {

                if (isCustom) {
                    return;
                }

                const bypass = document.querySelector('.ag-post-metabox [name="ag_settings[bypass]"]');
                const restrict = document.querySelector('.ag-post-metabox [name="ag_settings[restrict]"]');

                input.value = agagemap[e.target.value];
                title.dataset.age = agagemap[e.target.value];

                const {
                    dataset: {
                        age,
                        textRestrict,
                    }
                } = title;

                if (restrict && !restrict.checked) {
                    return;
                }

                if (bypass && bypass.checked) {
                    return;
                }

                text.textContent = String.format(textRestrict, age);
                // alert(agagemap[e.target.value]);
            });
        }
    }
    //
});
