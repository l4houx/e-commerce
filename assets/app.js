import './bootstrap.js';

import { Toast } from 'bootstrap';

const toasts = document.getElementById('liveToast');
if (toasts) {
    toasts.forEach((toast) => {
        (new Toast(toast, { delay: 5000 })).show();
    });
}

//import 'bootstrap';
import 'htmx.org';
//import './vendor/bootstrap/dist/css/bootstrap.min.css';
import '@fortawesome/fontawesome-free/css/all.css';
import './styles/app.css';
