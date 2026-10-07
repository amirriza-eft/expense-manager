<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?= base_url('assets/css/transactions.css') ?>">

<div id="transactions-app">
    <?php $this->load->view('transactions/filters'); ?>
    <?php $this->load->view('transactions/toolbar'); ?>
    <?php $this->load->view('transactions/list'); ?>
    <?php $this->load->view('transactions/deleted_modal'); ?>
    <?php $this->load->view('transactions/modal'); ?>
    <?php $this->load->view('transactions/delete_modal'); ?>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js"></script>
<!-- Remove the next two lines if your layout already loads Vue 3 and axios -->
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<?php $this->load->view('categories/category_manager'); ?>

<script>
const transactionsApp = Vue.createApp({
    data() {
        return {
            transactions: [],
            deletedTransactions: [],
            categoryOptions: [],

            filters: {search: '', type: '', category_id: '', from_date: '', to_date: '', sort: 'newest'},

            transactionForm: {id: '', title: '', type: 'expense', category_id: '', amount: '', description: '', transaction_date: '', date_display: ''},
            transactionLoading: true,
            transactionMessage: '',
            transactionMessageSuccess: false,

            currentPage: 1,
            totalPages: 1,
            pageNumbers: [],
            totalCount: null,

            deleteTransactionId: null,

            expanded: {},
            overflowing: {}
        };
    },

    methods: {
        fmtNumber(value) {
            return formatNumber(value);
        },

        fmtDate(date) {
            return formatPersianDate(date);
        },

        amountHtml(tx) {
            return formatAmount(tx.amount, tx.type);
        },

        errorText(error) {
            if (error.response && error.response.data && error.response.data.message) {
                return error.response.data.message;
            }
            return 'خطا در ارتباط با سرور';
        },

        refreshDashboard() {
            if (typeof loadDashboardSummary === 'function') loadDashboardSummary();
        },

        showModal(ref) {
            bootstrap.Modal.getOrCreateInstance(this.$refs[ref]).show();
        },

        hideModal(ref) {
            const modal = bootstrap.Modal.getInstance(this.$refs[ref]);
            if (modal) modal.hide();
        },

        cleanupModal() {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        },

        toggleDescription(id) {
            this.expanded[id] = !this.expanded[id];
        },

        checkDescriptions() {
            this.checkList(this.$refs.transactionList);
            this.checkList(this.$refs.deletedList);
        },

        checkList(list) {
            if (!list) return;
            const cards = list.querySelectorAll('.transaction-card');
            for (const card of cards) {
                const desc = card.querySelector('.transaction-card__description');
                if (desc && desc.scrollHeight > desc.clientHeight + 2) {
                    this.overflowing[card.dataset.transactionId] = true;
                }
            }
        },

        initDatePickers() {
            $(this.$refs.filterFromDate).pDatepicker({
                format: 'YYYY/MM/DD', autoClose: true, initialValue: false, calendar: {persian: {locale: 'fa'}},
                onSelect: () => { this.filters.from_date = this.$refs.filterFromDate.value; }
            });

            $(this.$refs.filterToDate).pDatepicker({
                format: 'YYYY/MM/DD', autoClose: true, initialValue: false, calendar: {persian: {locale: 'fa'}},
                onSelect: () => { this.filters.to_date = this.$refs.filterToDate.value; }
            });

            $(this.$refs.txDateDisplay).pDatepicker({
                format: 'YYYY/MM/DD', autoClose: true, initialValue: true, calendar: {persian: {locale: 'fa'}},
                onSelect: () => {
                    const shamsi = this.$refs.txDateDisplay.value;
                    this.transactionForm.date_display = shamsi;
                    this.transactionForm.transaction_date = convertPersianToGregorian(shamsi);
                }
            });
        },

        async loadCategoryOptions() {
            try {
                const response = await axios.get("<?= site_url('api/categories') ?>");
                const data = response.data;
                if (data.status) this.categoryOptions = data.categories || [];
            } catch (error) {
                console.error(error);
            }
        },

        async loadTransactions(page) {
            this.currentPage = page || 1;
            this.transactionLoading = true;

            try {
                const response = await axios.get("<?= site_url('api/transaction') ?>", {
                    params: {
                        page: this.currentPage,
                        search: this.filters.search,
                        type: this.filters.type,
                        category_id: this.filters.category_id,
                        from_date: this.filters.from_date ? convertPersianToGregorian(this.filters.from_date) : '',
                        to_date: this.filters.to_date ? convertPersianToGregorian(this.filters.to_date) : '',
                        sort: this.filters.sort || 'newest'
                    }
                });
                const data = response.data;

                if (data.status && data.transactions && data.transactions.length) {
                    const pagination = data.pagination || {};

                    this.transactions = data.transactions;
                    this.totalCount = pagination.total || data.transactions.length;
                    this.totalPages = Number(pagination.total_pages) || 1;
                    this.currentPage = Number(pagination.current_page) || this.currentPage;
                    this.expanded = {};

                    this.pageNumbers = [];
                    const start = Math.max(1, this.currentPage - 2);
                    const end = Math.min(this.totalPages, this.currentPage + 2);
                    for (let i = start; i <= end; i++) {
                        this.pageNumbers.push(i);
                    }

                    this.$nextTick(this.checkDescriptions);
                } else {
                    this.transactions = [];
                    this.totalPages = 1;
                    this.totalCount = 0;
                    this.pageNumbers = [];
                }
            } catch (error) {
                console.error(error);
            }

            this.transactionLoading = false;
        },

        onFilterTypeChange() {
            this.filters.category_id = '';
            this.loadTransactions(1);
        },

        async loadDeletedTransactions() {
            try {
                const response = await axios.get("<?= site_url('api/transaction/deleted') ?>");
                const data = response.data;

                if (data.status) {
                    this.deletedTransactions = data.transactions || [];
                    this.$nextTick(this.checkDescriptions);
                }
            } catch (error) {
                console.error(error);
            }
        },

        async restoreTransaction(id) {
            try {
                const response = await axios.post("<?= site_url('api/transaction/restore') ?>", new URLSearchParams({id: id}));
                const data = response.data;

                if (data.status) {
                    await this.loadTransactions(this.currentPage || 1);
                    this.loadDeletedTransactions();
                    this.refreshDashboard();
                } else {
                    alert(data.message || 'بازیابی انجام نشد');
                }
            } catch (error) {
                console.error(error);
                alert(this.errorText(error));
            }
        },

        setTransactionDate(gregorian) {
            const date = gregorian || todayGregorianDate();
            this.transactionForm.transaction_date = date;
            this.transactionForm.date_display = formatPersianDate(date);
        },

        openCreateTransactionModal() {
            this.transactionMessage = '';
            this.transactionForm = {id: '', title: '', type: 'expense', category_id: '', amount: '', description: '', transaction_date: '', date_display: ''};
            this.setTransactionDate();
        },

        openEditTransactionModal(tx) {
            let categoryId = '';
            for (const category of this.categoryOptions) {
                if (category.type === tx.type && String(category.id) === String(tx.category_id)) {
                    categoryId = tx.category_id;
                }
            }

            this.transactionMessage = '';
            this.transactionForm = {
                id: tx.id,
                title: tx.title,
                type: tx.type,
                category_id: categoryId,
                amount: formatAmountInput(tx.amount),
                description: tx.description || '',
                transaction_date: '',
                date_display: ''
            };
            this.setTransactionDate(tx.transaction_date);
            this.showModal('transactionModal');
        },

        onAmountInput(event) {
            const input = event.target;
            const oldLength = input.value.length;
            const position = input.selectionStart;

            input.value = formatAmountInput(input.value);

            const newPosition = position + input.value.length - oldLength;
            input.setSelectionRange(newPosition, newPosition);
            this.transactionForm.amount = input.value;
        },

        async saveTransaction() {
            const form = this.transactionForm;

            if (form.date_display) form.transaction_date = convertPersianToGregorian(form.date_display);

            if (!form.transaction_date) {
                this.transactionMessage = 'لطفاً تاریخ تراکنش را انتخاب کنید';
                this.transactionMessageSuccess = false;
                return;
            }

            const formData = new FormData();
            formData.append('id', form.id);
            formData.append('title', form.title);
            formData.append('type', form.type);
            formData.append('category_id', form.category_id);
            formData.append('amount', getAmountInputValue(form.amount));
            formData.append('description', form.description);
            formData.append('transaction_date', form.transaction_date);

            const isCreate = !form.id;
            let url = "<?= site_url('api/transaction/create') ?>";
            if (!isCreate) url = "<?= site_url('api/transaction/update/') ?>" + form.id;

            this.transactionLoading = true;

            try {
                const response = await axios.post(url, formData);
                const data = response.data;

                if (typeof data !== 'object') throw new Error('Invalid JSON');

                this.transactionMessage = data.message || '';
                this.transactionMessageSuccess = data.status ? true : false;

                if (data.status) {
                    this.$refs.transactionModal.addEventListener('hidden.bs.modal', this.cleanupModal, {once: true});
                    this.hideModal('transactionModal');

                    await this.loadTransactions(isCreate ? 1 : this.currentPage);
                    this.refreshDashboard();
                }
            } catch (error) {
                console.error(error);
                this.transactionMessage = this.errorText(error);
                this.transactionMessageSuccess = false;
            }

            this.transactionLoading = false;
        },

        confirmDeleteTransaction(id) {
            this.deleteTransactionId = id;
            this.showModal('deleteModal');
        },

        async deleteTransaction() {
            if (!this.deleteTransactionId) return;

            this.transactionLoading = true;

            try {
                const response = await axios.post(
                    "<?= site_url('api/transaction/delete/') ?>" + this.deleteTransactionId,
                    new URLSearchParams({id: this.deleteTransactionId})
                );
                const data = response.data;

                if (data.status) {
                    this.hideModal('deleteModal');
                    this.deleteTransactionId = null;

                    await this.loadTransactions(this.currentPage || 1);
                    this.loadDeletedTransactions();
                    this.refreshDashboard();
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error(error);
                alert(this.errorText(error));
            }

            this.transactionLoading = false;
        }
    },

    mounted() {
        window.reloadCategoryOptions = this.loadCategoryOptions;

        this.loadCategoryOptions();
        this.loadTransactions(1);
        this.initDatePickers();

        this.$refs.deletedModal.addEventListener('shown.bs.modal', this.checkDescriptions);
    }
});

document.addEventListener('DOMContentLoaded', () => transactionsApp.mount('#transactions-app'));

</script>
