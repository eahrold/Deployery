<!-- Trigger the modal with a button -->
<a data-toggle="modal" data-target="#pubKey">Your Public Key <i class="fa fa-key" aria-hidden="true"></i></a>

<!-- Modal -->
<div class="modal fade" id="pubKey" role="dialog">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-body">
        <textarea rows='12' readonly>{{ trim(Auth::user()->pub_key) }}</textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
