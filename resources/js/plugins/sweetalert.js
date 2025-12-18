import Swal from 'sweetalert2'

/**
 * Success popup
 */
export const successAlert = (message) => {
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: message,
        timer: 2000,
        showConfirmButton: false,
    })
}

/**
 * Error popup
 */
export const errorAlert = (message) => {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: message,
    })
}

/**
 * Delete confirmation popup
 */
export const confirmDelete = () => {
    return Swal.fire({
        title: 'Are you sure?',
        text: 'This record will be permanently deleted!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel',
    })
}
