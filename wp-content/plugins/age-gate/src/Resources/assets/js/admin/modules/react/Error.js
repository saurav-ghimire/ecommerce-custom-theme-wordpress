import React from 'react';

export const Error = () => (
    <div className="age-gate-gallery__error">
        <i className="dashicons dashicons-warning"></i>

        <p>Could not communicate with the REST API.</p>

        <p>Do you have permission to view, or could another plugin blocking access?</p>
    </div>
)
