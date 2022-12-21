import React, { useState, useEffect } from "react";
import { Error } from "./Error";


const Gallery = (type = null) => {
    const [selected, setSelected] = useState();
    const [items, setItems] = useState([]);
    const [page, setPage] = useState(1);
    const [perpage, setPerpage] = useState();
    const [total, setTotal] = useState();
    const [next, setNext] = useState(1);
    const [types, setTypes] = useState(['image', 'video']);
    const [search, setSearch] = useState('');
    const [error, setError] = useState(false);
    const [working, setWorking] = useState(false);

    const update = () => {

        const { agPreSelected } = window;
        setSelected(agPreSelected);
    }

    useEffect(() => {
        window.addEventListener('ag_gallery_update', update);

        return () => {
            window.removeEventListener('ag_gallery_update', update);
        };
    }, []);

    useEffect(() => {
        update();
    }, []);

    useEffect(() => {
        setTypes(type);
    }, []);

    useEffect(() => {
        if (search) {
            return;
        }
        setWorking(true);
        const { agPreSelected } = window;

        const {
            ag_admin: {
                nonce,
                rest_url,
            }
        } = window;


        const url = new URL(rest_url);

        url.searchParams.append('page', page);
        url.searchParams.append('in', agPreSelected);

        console.log(url);

        fetch(url.href, {
            headers: {
                'X-WP-Nonce': nonce
            }
        })
        .then(r => r.json())
        .then((data) => {

            const {
                assets,
                perPage,
                total,
                next,
                data: {
                    status
                }
            } = data;

            if (status !== 200) {
                setError(true);
            } else {

                setItems(items.concat(assets));
                setNext(next);
                setTotal(total);
                setPerpage(perPage);
            }
            setWorking(false);
        })
        .catch(err => setError(err));
    }, [page, search]);

    useEffect(() => {
        if (search.length < 3) {
            return;
        }
        setWorking(true);
        const { ag_admin: { nonce, rest_url } } = window;

        const url = new URL(rest_url);

        url.searchParams.append('page', page);
        url.searchParams.append('in', agPreSelected);

        fetch(url.href, {
            headers: {
                'X-WP-Nonce': nonce
            }
        })
        .then(r => r.json())
        .then((data) => {
            const {
                assets,
            } = data;

            setItems(assets);
            setWorking(false);
        })
        .catch(err => setError(true));
    }, [search]);

    const updateScroll = () => {
        const element = document.querySelector(".age-gate-gallery__items");
        element.scrollTop = element.scrollHeight;
    }

    const {
        ag_admin: {
            labels: {
                search: searchLabel,
                more,
                loading,
            }
        }
    } = window;


    return (
        <div className="age-gate-gallery">
            <div className="age-gate-gallery__toolbar">
                <input
                    type="search"
                    placeholder={searchLabel}
                    onChange={(e) => setSearch(e.target.value)}
                />
            </div>
            { items && items.length ?
                <>
                    <div className="age-gate-gallery__items">
                        {items.map((item) => {
                            const {
                                id,
                                thumbnail,
                                full,
                                type,
                            } = item;

                            return (
                                <div key={id} className="age-gate-gallery__item">
                                    <label className="age-gate-gallery__label">
                                        <input
                                            type="radio"
                                            name="media"
                                            value={id}
                                            onChange={() => setSelected(id)}
                                            checked={selected == id}
                                            data-full={full}
                                            data-type={type}
                                        />
                                        <span className="age-gate-gallery__inner">
                                            <img
                                                src={thumbnail}
                                                alt=""
                                                onLoad={() => updateScroll()}
                                            />
                                        </span>
                                    </label>
                                </div>
                            );
                        })}
                    </div>
                    <p>{Math.min(items.length, total)} / {total}</p>
                    {next ? <button type="button" className="button button-primary" disabled={working} onClick={() => setPage(page + 1)}>{working ? loading : more}</button> : ''}
                </>
            : ''}
            { error ? <Error /> : ''}
        </div>
    );
}

// export { setSelected };
export default Gallery;
