import axios from 'axios';
import toastr from 'toastr';
window.axios = axios;
window.toastr = toastr;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
