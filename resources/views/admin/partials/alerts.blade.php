<script>
    $(document).ready(function() {
        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if (session('warning'))
            toastr.warning("{{ session('warning') }}");
        @endif
        @if (session('info'))
            toastr.info("{{ session('info') }}");
        @endif
    });
</script>


{{-- Modal Delete Confirmation --}}
<!-- Delete Confirmation -->
<div class="modal fade" id="modalDelete">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-filled">
            <div class="modal-body p-4">
                <div class="text-center">
                    <span class="rounded-circle d-inline-flex p-2 bg-danger-transparent mb-2"><i class="ti ti-trash fs-24 text-danger"></i></span>
                    <i class="dripicons-checkmark h1"></i>
                    <h4 class="mt-2">Are you sure want to delete ?</h4>
                    <p class="mt-3">This Item Will Be Deleted Permanently. You Can Not Undo This Action.</p>
                    <form action="" method="post">
                        @csrf
                        @method('delete')
                        <button type="button" class="btn me-2 btn-secondary fs-13 fw-medium p-2 px-3 shadow-none" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary fs-13 fw-medium p-2 px-3">Yes Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Delete Confirmation Modal -->
<div class="modal fade" id="modalBulkDelete">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-filled">
            <div class="modal-body p-4">
                <div class="text-center">
                    <span class="rounded-circle d-inline-flex p-2 bg-danger-transparent mb-2">
                        <i class="ti ti-trash fs-24 text-danger"></i>
                    </span>
                    <h4 class="mt-2">Are you sure want to delete selected items?</h4>
                    <p class="mt-3">These items will be deleted permanently. You can’t undo this action.</p>
                    <button type="button" class="btn me-2 btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirm-bulk-delete">Yes Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>
