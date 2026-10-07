<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?= base_url('assets/css/transactions.css') ?>">

<div id="transactions-app">
    <?php $this->load->view('transactions/filters'); ?>
    <?php $this->load->view('transactions/toolbar'); ?>
    <?php $this->load->view('transactions/list'); ?>
    <?php $this->load->view('transactions/deleted_modal'); ?>
    <?php $this->load->view('transactions/modal'); ?>
    <?php $this->load->view('transactions/delete_modal'); ?>
    <?php $this->load->view('categories/category_manager'); ?>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js"></script>
<!-- Remove the next two lines if your layout already loads Vue 3 and axios -->
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
function emptyTransactionForm() {
    return {id: '', title: '', type: 'expense', category_id: '', amount: '', description: '', transaction_date: '', date_display: ''};
}

const {createApp} = Vue;

const app = createApp({
    data() {
        return {
            // categories
            categories: [],
            deletedCategories: [],
            categoryForm: {id: '', title: '', type: 'expense'},
            categoryLoading: false,
            categoryMessage: '',
            categoryMessageSuccess: false,

            // transactions
            transactions: [],
            deletedTransactions: [],
            filters: {search: '', type: '', category_id: '', from_date: '', to_date: '', sort: 'newest'},
            transactionForm: emptyTransactionForm(),
            transactionLoading: true,
            transactionMessage: '',
            transactionMessageSuccess: false,
            currentPage: 1,
            totalPages: 1,
            totalCount: null,
            deleteTransactionId: null,
            expanded: {},
            overflowing: {}
        };
    },

    computed: {
        expenseCategories() {
            return this.categories.filter(c => c.type === 'expense');
        },
        incomeCategories() {
            return this.categories.filter(c => c.type === 'income');
        },
        deletedExpenseCategories() {
            return this.deletedCategories.filter(c => c.type === 'expense');
        },
        deletedIncomeCategories() {
            return this.deletedCategories.filter(c => c.type === 'income');
        },
        filterCategories() {
            return this.categories.filter(c => !this.filters.type || c.type === this.filters.type);
        },
        formCategories() {
            return this.categories.filter(c => c.type === this.transactionForm.type);
        },
        pageNumbers() {
            const pages = [];
            const start = Math.max(1, this.currentPage - 2);
            const end = Math.min(this.totalPages, this.currentPage + 2);
            for (let i = start; i <= end; i++) pages.push(i);
            return pages;
        }
    },

    methods: {
        // ---------- helpers ----------
        fmtNumber(value) {
            return formatNumber(value);
        },

        fmtDate(date) {
            return typeof formatPersianDate === 'function' ? formatPersianDate(date) : (date || '');
        },

        amountHtml(tx) {
            return typeof formatAmount === 'function'
                ? formatAmount(tx.amount, tx.type)
                : (tx.type === 'income' ? '+' : '-') + Number(tx.amount).toLocaleString('fa-IR');
        },

        typeClass(type) {
            return type === 'income' ? 'income' : 'expense';
        },

        typeIcon(type) {
            return type === 'income' ? 'bi-arrow-down-left' : 'bi-arrow-up-right';
        },

        errorText(error) {
            return (error.response && error.response.data && error.response.data.message) || 'خطا در ارتباط با سرور';
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

        refreshDashboard() {
            if (typeof loadDashboardSummary === 'function') loadDashboardSummary();
        },

        toggleDescription(id) {
            this.expanded[id] = !this.expanded[id];
        },

        // shows the "more/less" button only for descriptions that are actually clamped
        checkDescriptions() {
            [this.$refs.transactionList, this.$refs.deletedList].forEach(list => {
                if (!list) return;
                list.querySelectorAll('.transaction-card').forEach(card => {
                    const desc = card.querySelector('.transaction-card__description');
                    if (desc && desc.scrollHeight > desc.clientHeight + 2) {
                        this.overflowing[card.dataset.transactionId] = true;
                    }
                });
            });
        },

        initDatePickers() {
            const options = {format: 'YYYY/MM/DD', autoClose: true, initialValue: false, calendar: {persian: {locale: 'fa'}}};

            $(this.$refs.filterFromDate).pDatepicker({
                ...options,
                onSelect: () => { this.filters.from_date = this.$refs.filterFromDate.value; }
            });

            $(this.$refs.filterToDate).pDatepicker({
                ...options,
                onSelect: () => { this.filters.to_date = this.$refs.filterToDate.value; }
            });

            $(this.$refs.txDateDisplay).pDatepicker({
                ...options,
                initialValue: true,
                onSelect: () => {
                    const shamsi = this.$refs.txDateDisplay.value;
                    this.transactionForm.date_display = shamsi;
                    this.transactionForm.transaction_date = convertPersianToGregorian(shamsi);
                }
            });
        },

        // ---------- categories ----------
        setCategoryMessage(text, success) {
            this.categoryMessage = text;
            this.categoryMessageSuccess = !!success;
        },

        async loadCategories() {
            try {
                const response = await axios.get("<?= site_url('api/categories') ?>");
                const data = response.data;
                if (data.status) this.categories = data.categories || [];
            } catch (error) {
                console.error(error);
            }
        },

        async loadDeletedCategories() {
            try {
                const response = await axios.get("<?= site_url('api/categories/deleted') ?>");
                const data = response.data;
                if (data.status) this.deletedCategories = data.categories || [];
            } catch (error) {
                console.error(error);
            }
        },

        openCategoryManager() {
            this.categoryMessage = '';
            this.loadDeletedCategories();
        },

        resetCategoryForm() {
            this.categoryForm = {id: '', title: '', type: 'expense'};
        },

        editCategory(category) {
            this.categoryMessage = '';
            this.categoryForm = {id: category.id, title: category.title, type: category.type};
            this.$refs.categoryTitle.focus();
        },

        async saveCategory() {
            const title = this.categoryForm.title.trim();
            if (!title) return;

            const url = this.categoryForm.id
                ? "<?= site_url('api/categories/update/') ?>" + this.categoryForm.id
                : "<?= site_url('api/categories/create') ?>";

            this.categoryLoading = true;

            try {
                const response = await axios.post(url, new URLSearchParams({title: title, type: this.categoryForm.type}));
                const data = response.data;
                this.setCategoryMessage(data.message || (data.status ? 'دسته‌بندی ذخیره شد' : 'خطا در ذخیره دسته‌بندی'), data.status);

                if (data.status) {
                    this.resetCategoryForm();
                    await this.loadCategories();
                }
            } catch (error) {
                this.setCategoryMessage(this.errorText(error), false);
            } finally {
                this.categoryLoading = false;
            }
        },

        async deleteCategory(id) {
            if (!confirm('آیا از حذف این دسته‌بندی اطمینان دارید؟')) return;

            this.categoryLoading = true;

            try {
                const response = await axios.post("<?= site_url('api/categories/delete/') ?>" + id);
                const data = response.data;
                this.setCategoryMessage(data.message || (data.status ? 'دسته‌بندی حذف شد' : 'خطا در حذف دسته‌بندی'), data.status);

                if (data.status) {
                    await this.loadCategories();
                    await this.loadDeletedCategories();
                }
            } catch (error) {
                this.setCategoryMessage(this.errorText(error), false);
            } finally {
                this.categoryLoading = false;
            }
        },

        async restoreCategory(id) {
            this.categoryLoading = true;

            try {
                const response = await axios.post("<?= site_url('api/categories/restore/') ?>" + id);
                const data = response.data;
                this.setCategoryMessage(data.message || (data.status ? 'دسته‌بندی بازیابی شد' : 'خطا در بازیابی دسته‌بندی'), data.status);

                if (data.status) {
                    await this.loadCategories();
                    await this.loadDeletedCategories();
                }
            } catch (error) {
                this.setCategoryMessage(this.errorText(error), false);
            } finally {
                this.categoryLoading = false;
            }
        },

        // ---------- transactions ----------
        setTransactionMessage(text, success) {
            this.transactionMessage = text;
            this.transactionMessageSuccess = !!success;
        },

        async loadTransactions(page) {
            this.currentPage = page || 1;
            this.transactionLoading = true;

            const f = this.filters;

            try {
                const response = await axios.get("<?= site_url('api/transaction') ?>", {
                    params: {
                        page: this.currentPage,
                        search: f.search,
                        type: f.type,
                        category_id: f.category_id,
                        from_date: f.from_date ? convertPersianToGregorian(f.from_date) : '',
                        to_date: f.to_date ? convertPersianToGregorian(f.to_date) : '',
                        sort: f.sort || 'newest'
                    }
                });
                const data = response.data;

                if (!data.status || !data.transactions || !data.transactions.length) {
                    this.transactions = [];
                    this.totalPages = 1;
                    this.totalCount = 0;
                    return;
                }

                const pagination = data.pagination || {};

                this.transactions = data.transactions;
                this.totalCount = pagination.total != null ? pagination.total : data.transactions.length;
                this.totalPages = Number(pagination.total_pages) || 1;
                this.currentPage = Number(pagination.current_page) || this.currentPage;
                this.expanded = {};
                this.$nextTick(this.checkDescriptions);
            } catch (error) {
                console.error(error);
            } finally {
                this.transactionLoading = false;
            }
        },

        onFilterTypeChange() {
            this.filters.category_id = '';
            this.loadTransactions(1);
        },

        async loadDeletedTransactions() {
            try {
                const response = await axios.get("<?= site_url('api/transaction/deleted') ?>");
                const data = response.data;
                if (!data.status) return;

                this.deletedTransactions = data.transactions || [];
                this.$nextTick(this.checkDescriptions);
            } catch (error) {
                console.error(error);
            }
        },

        async restoreTransaction(id) {
            id = Number(id);
            if (!id) return;

            try {
                const response = await axios.post("<?= site_url('api/transaction/restore') ?>", new URLSearchParams({id: id}));
                const data = response.data;

                if (!data.status) {
                    alert(data.message || 'بازیابی انجام نشد');
                    return;
                }

                await this.loadTransactions(this.currentPage || 1);
                this.loadDeletedTransactions();
                this.refreshDashboard();
            } catch (error) {
                console.error(error);
                alert(this.errorText(error));
            }
        },

        setTransactionDate(gregorian) {
            const g = gregorian || (typeof todayGregorianDate === 'function'
                ? todayGregorianDate()
                : new Date().toISOString().split('T')[0]);

            this.transactionForm.transaction_date = g;
            this.transactionForm.date_display = typeof formatPersianDate === 'function' ? formatPersianDate(g) : g;
        },

        openCreateTransactionModal() {
            this.transactionForm = emptyTransactionForm();
            this.transactionMessage = '';
            this.setTransactionDate();
        },

        openEditTransactionModal(tx) {
            const categoryExists = this.categories.some(c => c.type === tx.type && String(c.id) === String(tx.category_id));

            this.transactionMessage = '';
            this.transactionForm = {
                id: tx.id,
                title: tx.title,
                type: tx.type,
                category_id: categoryExists ? tx.category_id : '',
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
            const f = this.transactionForm;

            if (f.date_display) f.transaction_date = convertPersianToGregorian(f.date_display);

            if (!f.transaction_date) {
                this.setTransactionMessage('لطفاً تاریخ تراکنش را انتخاب کنید', false);
                return;
            }

            const formData = new FormData();
            formData.append('id', f.id);
            formData.append('title', f.title);
            formData.append('type', f.type);
            formData.append('category_id', f.category_id);
            formData.append('amount', getAmountInputValue(f.amount));
            formData.append('description', f.description);
            formData.append('transaction_date', f.transaction_date);

            const isCreate = !f.id;
            const url = isCreate
                ? "<?= site_url('api/transaction/create') ?>"
                : "<?= site_url('api/transaction/update/') ?>" + f.id;

            this.transactionLoading = true;

            try {
                const response = await axios.post(url, formData);
                const data = response.data;

                if (typeof data !== 'object' || data === null) throw new Error('Invalid JSON');

                this.setTransactionMessage(data.message || '', data.status);
                if (!data.status) return;

                this.$refs.transactionModal.addEventListener('hidden.bs.modal', this.cleanupModal, {once: true});
                this.hideModal('transactionModal');

                await this.loadTransactions(isCreate ? 1 : this.currentPage);
                this.refreshDashboard();
            } catch (error) {
                console.error(error);
                this.setTransactionMessage(this.errorText(error), false);
            } finally {
                this.transactionLoading = false;
            }
        },

        confirmDeleteTransaction(id) {
            this.deleteTransactionId = Number(id);
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

                if (!data.status) {
                    alert(data.message);
                    return;
                }

                this.hideModal('deleteModal');
                this.deleteTransactionId = null;

                await this.loadTransactions(this.currentPage || 1);
                this.loadDeletedTransactions();
                this.refreshDashboard();
            } catch (error) {
                console.error(error);
                alert(this.errorText(error));
            } finally {
                this.transactionLoading = false;
            }
        }
    },

    mounted() {
        this.loadCategories();
        this.loadTransactions(1);
        this.initDatePickers();

        this.$refs.categoryModal.addEventListener('hidden.bs.modal', this.resetCategoryForm);
        this.$refs.deleteModal.addEventListener('hidden.bs.modal', () => { this.deleteTransactionId = null; });
        this.$refs.deletedModal.addEventListener('shown.bs.modal', this.checkDescriptions);
    }
});

// keep the exact same spacing between inline elements as the old HTML
app.config.compilerOptions.whitespace = 'preserve';

// mount after the page (and its helper scripts) finished loading
document.addEventListener('DOMContentLoaded', () => app.mount('#transactions-app'));
</script>
