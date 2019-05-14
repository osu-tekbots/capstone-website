let _snackbarQueue = [];
let _sb = null;
let _showing = false;

document.addEventListener('DOMContentLoaded', () => {
    let eBody = document.getElementsByTagName('body')[0];
    _sb = document.createElement('div');
    _sb.id = 'snackbar';
    eBody.appendChild(_sb);
});

/**
 * Displays a snackbar notification.
 * 
 * @param {string} message the message to display
 * @param {string} type the type of message. Will determine the color and icon used in the snackbar.
 */
function snackbar(message, type = 'info') {
    let item = {message, type};
    _snackbarQueue.push(item);
    if (!_showing) {
        _showSnackbar();
    }
}

function _clearSnackbar() {
    _sb.className = '';
    while (_sb.firstChild) {
        _sb.removeChild(_sb.firstChild);
    }
}

function _showSnackbar() {
    _showing = true;
    let item = _snackbarQueue.shift();
    _setSnackbarStyle(_sb, item.type);
    _sb.appendChild(document.createTextNode(item.message));
    _sb.className += ' show';
    setTimeout(function () {
        _clearSnackbar();
        _showing = false;
        if (_snackbarQueue.length > 0) {
            setTimeout(function () {
                _showSnackbar();
            }, 100);
        }
    }, 3000);
}

function _setSnackbarStyle(el, type) {
    let i = document.createElement('i');
    switch (type) {
        case 'error':
            i.classList.add('fas', 'fa-exclamation-circle');
            el.className = 'error';
            break;
        case 'warn':
            i.classList.add('fas', 'fa-exclamation-triangle');
            el.className = 'warn';
            break;
        case 'info':
            i.classList.add('fas', 'fa-exclamation-circle');
            el.className = 'info';
            break;
        case 'success':
            i.classList.add('fas', 'fa-check-circle');
            el.className = 'success';
            break;
        default:
            break;
    }
    el.appendChild(i);
}