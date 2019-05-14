// Simple object that provides nice, clean functions for making asynchronous HTTP requests to our backend
const api = {
    get: url => request('GET', url),

    post: (url, data = {}, encoded = false) => request('POST', url, data, encoded),

    patch: (url, data = {}, encoded = false) => request('PATCH', url, data, encoded),

    delete: url => request('DELETE', url)
};

/**
 * Makes an AJAX request to the API endpoint for the website.
 * 
 * @param {string} method the HTTP method to use in the request (GET, POST, PUT, DELETE)
 * @param {string} url the resource URL location. A base prefix of `api` will be applied to the url.
 * @param {object|FormData|undefined} data the body to send in the request
 * @param {boolean} encoded whether the request body is being sent as `multipart/form-data` or 
 * `application/x-www-form-urlencoded`
 */
function request(method, url, data, encoded) {
    return new Promise((resolve, reject) => {
        let xhr = new XMLHttpRequest();
        xhr.onload = function () {
            let data;
            try {        
                data = JSON.parse(this.response);
            } catch(err) {
                console.log(err);
                reject(new Error('Failed to parse response from server'));
            }
            if (this.status >= 200 && this.status < 300) {
                return resolve(data);
            } else {
                return reject(data);
            }
        };
        xhr.open(method, 'api' + url, true);
        if (data) {
            if (encoded) {
                xhr.send(data);
            } else {
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.send(JSON.stringify(data));
            }
        } else {
            xhr.send();
        }
    });
}