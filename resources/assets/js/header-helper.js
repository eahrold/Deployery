// TODO: Remove all this junk and get it working with
// the Vue.http intercepter.
window.getGlobalApiHeaders = function (){
    var token = document.head.querySelector("[name=api-token]").content;
    var csrf = document.head.querySelector("[name=csrf-token]").content;
    if(token && csrf){
        return  {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf,
            'Authorization': 'Bearer '+token
        }
    }
    return {};
};
