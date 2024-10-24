

import swal from 'sweetalert2/dist/sweetalert2.esm.min'
import '@sweetalert2/theme-dark/dark.css'
window.swal = swal;
window.toast = swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = swal.stopTimer;
        toast.onmouseleave = swal.resumeTimer;
    }
});
