import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App';
import { install } from 'resize-observer';
import './static/scss/style.scss';

if (!window.ResizeObserver) {
    try {
        install();
    } catch (e) {
        console.warn('ResizeObserver installation failed: ', e);
    }
}

const root = ReactDOM.createRoot(document.getElementById('root') as HTMLElement);
root.render(
    <React.StrictMode>
        <App />
    </React.StrictMode>,
);
