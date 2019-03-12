<template>
    <div class="modal fade" id="serverPubKey" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <span>Add this to <span id='sshkey-modal-username'>{{ user }}'s </span><code class='pl-1'> ~/.ssh/authorized_keys </code> file
            <span id='sshkey-modal-hostname'>on {{ host }}</span></span>
          </div>
          <div class="modal-body">
            <pub-key :pub-key='pubkey'></pub-key>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
</template>

<script>
    export default {
        props: {
            endpoint: {
                type: String,
                required: true
            }
        },

        mounted () {
            $("#serverPubKey").on('hidden.bs.modal',(e)=>{
                this.modalClosed();
            });

            $('#serverPubKey').on('show.bs.modal',(e)=>{
                var button = $(e.relatedTarget);
                this.user = button.data('user');
                this.host = button.data('host');
                this.getKey();
            });
        },

        data () {
            return {
                pubkey: null,
                user: null,
                host: null,
            }
        },

        methods : {
            getKey () {
                this.$http.get(this.endpoint + '/pubkey').then((response)=>{
                    this.pubkey = response.data.key;
                }, ({response}) => {
                    console.error('error getting pubkey');
                });
            },

            modalClosed () {
                this.pubkey = null;
            }
        }
    }
</script>