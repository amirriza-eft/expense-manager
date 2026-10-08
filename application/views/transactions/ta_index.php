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

<?php $this->load->view('categories/category_manager'); ?>

<script>
    const transactionsApp = Vue.createApp({

        data() {
            return {
                transactions: [],
                deletedTransactions: [],
                categoryOptions: [],

                filters: {search: '', type: '', category_id: '', from_date: '', to_date: '', sort: 'newest'},

                transactionForm: {id: '', title: '', type: 'expense', category_id: '', amount: '', description: '', date_display: ''},

                transactionLoading: false,
                transactionMessage: '',
                transactionMessageSuccess: false,

                currentPage: 1,
                totalPages: 1,
                pageNumbers: [],
                totalCount: 0,

                deleteTransactionId: null,
            };
        },

        methods: {

            fmtNumber(value) { return formatNumber(value); },
            fmtDate(date) { return formatPersianDate(date); },
            amountHtml(tx) { return formatAmount(tx.amount, tx.type); },

            cleanupModal() {
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');
            },

            initDatePickers() {
                $(this.$refs.filterFromDate).pDatepicker({
                    format: 'YYYY/MM/DD',
                    autoClose: true,
                    initialValue: false,
                    calendar: {persian: {locale: 'fa'}},
                    onSelect: () => {
                        this.filters.from_date = this.$refs.filterFromDate.value;
                    }
                });

                $(this.$refs.filterToDate).pDatepicker({
                    format: 'YYYY/MM/DD',
                    autoClose: true,
                    initialValue: false,
                    calendar: {persian: {locale: 'fa'}},
                    onSelect: () => {
                        this.filters.to_date = this.$refs.filterToDate.value;
                    }
                });

                $(this.$refs.txDateDisplay).pDatepicker({
                    format: 'YYYY/MM/DD',
                    autoClose: true,
                    initialValue: true,
                    calendar: {persian: {locale: 'fa'}},
                    onSelect: () => {
                        this.transactionForm.date_display = this.$refs.txDateDisplay.value;
                    }
                });
            },

            async loadCategoryOptions() {
                const response = await axios.get("<?= site_url('api/categories') ?>");

                if (response.data.status) {
                    this.categoryOptions = response.data.categories;
                }
            },

            async loadTransactions(page = 1) {
                this.currentPage = page;
                this.transactionLoading = true;

                try {
                    const response = await axios.get("<?= site_url('api/transaction') ?>", {
                        params: {
                            page: page,
                            search: this.filters.search,
                            type: this.filters.type,
                            category_id: this.filters.category_id,
                            from_date: this.filters.from_date ? convertPersianToGregorian(this.filters.from_date) : '',
                            to_date: this.filters.to_date ? convertPersianToGregorian(this.filters.to_date) : '',
                            sort: this.filters.sort
                        }
                    });

                    const data = response.data;

                    if (data.status && data.transactions.length) {
                        this.transactions = data.transactions;
                        this.totalCount = data.pagination.total;
                        this.totalPages = Number(data.pagination.total_pages);
                        this.currentPage = Number(data.pagination.current_page);

                        this.pageNumbers = [];
                        const start = Math.max(1, this.currentPage - 2);
                        const end = Math.min(this.totalPages, this.currentPage + 2);

                        for (let i = start; i <= end; i++) {
                            this.pageNumbers.push(i);
                        }

                    } else {
                        this.transactions = [];
                        this.totalCount = 0;
                        this.totalPages = 1;
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
                const response = await axios.get("<?= site_url('api/transaction/deleted') ?>");

                if (response.data.status) {
                    this.deletedTransactions = response.data.transactions;
                }
            },

            async restoreTransaction(id) {
                try {
                    const response = await axios.post(
                        "<?= site_url('api/transaction/restore') ?>",
                        new URLSearchParams({id: id})
                    );

                    if (response.data.status) {
                        await this.loadTransactions(this.currentPage);
                        this.loadDeletedTransactions();
                        loadDashboardSummary();
                    } else {
                        alert(response.data.message);
                    }
                } catch (error) {
                    console.error(error);
                    alert('خطا در ارتباط با سرور');
                }
            },

            openCreateTransactionModal() {
                this.transactionMessage = '';

                this.transactionForm = {
                    id: '',
                    title: '',
                    type: 'expense',
                    category_id: '',
                    amount: '',
                    description: '',
                    date_display: formatPersianDate(todayGregorianDate())
                };
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
                    date_display: formatPersianDate(tx.transaction_date)
                };

                bootstrap.Modal.getOrCreateInstance(this.$refs.transactionModal).show();
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
                const date = convertPersianToGregorian(form.date_display);

                if (!date) {
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
                formData.append('transaction_date', date);

                let url = "<?= site_url('api/transaction/create') ?>";

                if (form.id) {
                    url = "<?= site_url('api/transaction/update/') ?>" + form.id;
                }

                this.transactionLoading = true;

                try {
                    const response = await axios.post(url, formData);

                    this.transactionMessage = response.data.message;
                    this.transactionMessageSuccess = response.data.status;

                    if (response.data.status) {
                        bootstrap.Modal.getInstance(this.$refs.transactionModal).hide();

                        await this.loadTransactions(form.id ? this.currentPage : 1);
                        loadDashboardSummary();
                    }
                } catch (error) {
                    console.error(error);
                    this.transactionMessage = 'خطا در ارتباط با سرور';
                    this.transactionMessageSuccess = false;
                }

                this.transactionLoading = false;
            },

            confirmDeleteTransaction(id) {
                this.deleteTransactionId = id;
                bootstrap.Modal.getOrCreateInstance(this.$refs.deleteModal).show();
            },

            async deleteTransaction() {
                this.transactionLoading = true;

                try {
                    const response = await axios.post(
                        "<?= site_url('api/transaction/delete/') ?>" + this.deleteTransactionId,
                        new URLSearchParams({id: this.deleteTransactionId})
                    );

                    if (response.data.status) {
                        bootstrap.Modal.getInstance(this.$refs.deleteModal).hide();

                        await this.loadTransactions(this.currentPage);
                        this.loadDeletedTransactions();
                        loadDashboardSummary();
                    } else {
                        alert(response.data.message);
                    }
                } catch (error) {
                    console.error(error);
                    alert('خطا در ارتباط با سرور');
                }

                this.transactionLoading = false;
            }
        },

        mounted() {
            window.reloadCategoryOptions = this.loadCategoryOptions;

            this.loadCategoryOptions();
            this.loadTransactions();
            this.initDatePickers();

            this.$refs.transactionModal.addEventListener('hidden.bs.modal', this.cleanupModal);
            this.$refs.deleteModal.addEventListener('hidden.bs.modal', this.cleanupModal);
        }
    });

    transactionsApp.mount('#transactions-app');
</script>