import Swal from 'sweetalert2'


/**
 * Success popup
 */
export const successAlert = (message) => {

    return Swal.fire({
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

    return Swal.fire({
        icon: 'error',
        title: 'Error',
        text: message,
    })

}


/**
 * Delete confirmation
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

        reverseButtons: true,

    })

}


/**
 * Loading alert
 */
export const loadingAlert = (
    message = 'Processing...'
) => {

    Swal.fire({

        title: message,

        text: 'Please wait...',

        allowOutsideClick: false,

        allowEscapeKey: false,

        showConfirmButton: false,

        didOpen: () => {

            Swal.showLoading()

        },

    })

}


/**
 * Close only loading alert
 */
export const closeAlert = () => {

    if (
        Swal.isVisible() &&
        Swal.isLoading()
    ) {

        Swal.close()

    }

}


/**
 * Toast notification
 */
export const toastAlert = (
    message,
    icon = 'success'
) => {

    return Swal.fire({

        toast: true,

        position: 'top-end',

        icon: icon,

        title: message,

        showConfirmButton: false,

        timer: 2500,

        timerProgressBar: true,

        showCloseButton: true,

    })

}


/**
 * Unsaved changes confirmation
 */
export const confirmUnsavedChanges = () => {

    return Swal.fire({

        title: 'Unsaved changes',

        text: 'You have unsaved changes. Are you sure you want to leave this page?',

        icon: 'warning',

        showCancelButton: true,

        confirmButtonColor: '#d33',

        cancelButtonColor: '#6b7280',

        confirmButtonText: 'Leave page',

        cancelButtonText: 'Stay here',

        reverseButtons: true,

    })

}