<!-- Modal -->
<div class="modal fade" id="serverPubKey" role="dialog">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        Add this to <span id='sshkey-modal-username'>the user's </span> <code>~/.ssh/authorized_keys</code> file
        <span id='sshkey-modal-hostname'></span>
      </div>
      <div class="modal-body">
        <textarea rows='12' readonly>{{ trim( $project->pub_key ) }}</textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@section('js')
<script type="text/javascript">
  $(document).on("click", ".open-user-sshkey-modal", function () {
    console.log("Opening modal");
    var user = $(this).data('user');
    var host = $(this).data('host');

    if(user) {
      $("#sshkey-modal-username").text(user+"'s");
    }

    if(host) {
      $("#sshkey-modal-hostname").text('on ' + host);
    }
  });
</script>
@append