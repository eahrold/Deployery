let { Deployery } = window
let axios = window.axios = require('axios');

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
axios.defaults.headers.common['Accept'] = 'application/json'
axios.defaults.headers.common['Content-Type'] = 'application/json'

if (Deployery.apiToken) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${Deployery.apiToken}`
}

let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}


axios.interceptors.response.use(
    (response)=>{
        return response;
  },(error)=>{
        if(error.response.status === 401) {
            alert('Looks like your session has expired. reloading the page');
            window.location = window.location;
            return;
        }
        // Do something with response error
        return Promise.reject(error);
  }
);

export class ApiAdapter {
    get httpAdapter() {
        return axios;
    }

    post(endpoint, data) {
        return  this.httpAdapter.post(endpoint, data)
    }

    put(endpoint, data) {
        return this.httpAdapter.put(endpoint, data)
    }

    get (endpoint, params) {
        return  this.httpAdapter.get(endpoint, params).then((response)=>{
            const { data } = response

            const prevPage = _.get(data, 'meta.pagination.links.previous');
            const prev = prevPage ? ()=>{
                return this.get(prevPage)
            } : false

            const nextPage = _.get(data, 'meta.pagination.links.next');
            const next = nextPage ? ()=>{
                return this.get(nextPage)
            } : false

            return { data: data.data, pagination: { prev, next }, }
        })
    }

    projects() {
        return this.get(`/api/projects`)
    }

    project(id) {
        return this.httpAdapter.get(`/api/projects/${id}`)
    }
}

const Api = new ApiAdapter()

export default Api