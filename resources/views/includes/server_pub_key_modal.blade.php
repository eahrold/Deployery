<!-- Modal -->
<div class="modal fade" id="serverPubKey" role="dialog">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        Add this to the user's <code>~/.ssh/authorized_keys</code> file
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
