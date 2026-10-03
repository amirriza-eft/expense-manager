<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="modal fade"
     id="deleteTxConfirmModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-sm">

        <div class="modal-content glass-panel text-white"
             style="background:#1e1e24; border:1px solid var(--border-subtle);">

            <div class="modal-body text-center p-4">

                <i class="bi bi-exclamation-triangle text-danger fs-1 mb-2 d-block"></i>

                <h6 class="fw-bold mb-2">
                    حذف تراکنش
                </h6>

                <p class="text-muted small">
                    آیا از حذف این تراکنش از سوابق مالی اطمینان دارید؟
                </p>

                <div class="d-flex justify-content-center gap-2 mt-3">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        انصراف
                    </button>

                    <button
                        type="button"
                        class="btn btn-danger"
                        id="confirmDeleteTxBtn">
                        بله، حذف کن
                    </button>

                </div>
            </div>
        </div>
    </div>
</div>