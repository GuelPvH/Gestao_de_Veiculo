import './bootstrap';
import { initTomSelect } from './components/tom-select';

const boot = () => {
    initTomSelect();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}
