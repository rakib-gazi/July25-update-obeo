<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { router, useForm, usePage,Link } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import {computed, ref, watch} from "vue";
import {
    Combobox,
    ComboboxButton,
    ComboboxInput,
    ComboboxOption,
    ComboboxOptions,
    Dialog,
    DialogOverlay,
    DialogTitle,
    TransitionRoot
} from "@headlessui/vue";
import {CheckIcon, ChevronUpDownIcon} from "@heroicons/vue/20/solid/index.js";
// State
const userData = ref(usePage().props.adjustments);
const sources = ref(usePage().props.sources);
const isModalOpen = ref(false);
const isEditMode = ref(false);
const isSubmitting = ref(false);
let editingUserId = null;
// Type Input
const selectedType = ref(null);
const isTypeFocused = ref(false);
const hasTypeValue = computed(() => selectedType.value !== null);
const openModal = () => {
    isModalOpen.value = true;
};
const closeModal = () => {
    if (document.activeElement instanceof HTMLElement) {
        document.activeElement.blur();
    }
    isModalOpen.value = false;
    isEditMode.value = false;
    editingUserId = null;
    data.reset();
};
// Form
const data = useForm({
    month: '',
    purpose: '',
    type: '',
    source: '',
    amount: '',
});
// Refresh User List
const fetchUsers = () => {
    router.reload({
        only: ['adjustments'],
        onSuccess: () => {
            userData.value = usePage().props.adjustments ;
        }
    });
};

// Add or Update User
const handleSubmit = () => {
    console.log(data);
    isSubmitting.value = true;
    if (isEditMode.value) {
        data.put(`/dashboard/hotel-invoice/invoice-adjustment/${editingUserId}`, {
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Adjustment updated successfully',
                    showConfirmButton: false,
                    timer: 1000
                });
                closeModal();
                fetchUsers();
            },
            onFinish: () => isSubmitting.value = false
        });
    } else {
        data.post('/dashboard/hotel-invoice/invoice-adjustment', {
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Adjustment added successfully',
                    showConfirmButton: false,
                    timer: 1000
                });
                closeModal();
                fetchUsers();
            },
            onFinish: () => isSubmitting.value = false
        });
    }
};
// Month & Year picker state
const selectedMonth = ref("");
const selectedYear = ref("");
const currentYear = new Date().getFullYear();
const years = [];

// Last 5 years
for (let i = 5; i > 0; i--) {
    years.push(currentYear - i);
}

// Current year
years.push(currentYear);

// Next 10 years
for (let i = 1; i <= 10; i++) {
    years.push(currentYear + i);
}


// Sync selects into single YYYY-MM
watch([selectedMonth, selectedYear], ([m, y]) => {
    if (m && y) {
        data.month = `${y}-${String(m).padStart(2, "0")}`;
    }
});
// Prepare Edit
const handleEdit = (item) => {
    editingUserId = item.id;
    isEditMode.value = true;
    data.month = item.month;
    data.purpose = item.purpose;
    data.type = item.type;
    data.source = item.source;
    data.amount = item.amount;
    if (item.month) {
        const [year, month] = item.month.split('-');
        selectedYear.value = parseInt(year);
        selectedMonth.value = parseInt(month);
    }
    openModal();

};
const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});

// Delete
const handleDelete = (id) => {
    Swal.fire({
        title: 'Are you sure?',
        text: "This Adjustment will be deleted permanently!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.get(`/dashboard/hotel-invoice/invoice-adjustment/${id}`)
            Toast.fire({
                icon: "warning",
                title: "Adjustment Deleted successfully"
            });
        }
    });
};
const formatMonthYear = (monthValue) => {
    if (!monthValue) return '';
    const [year, month] = monthValue.split('-');
    const date = new Date(year, month - 1);
    return date.toLocaleString('default', { month: 'long', year: 'numeric' });
};

// Table Headers
const tableHeaders = [
    { text: 'Month', value: 'month' },
    { text: 'Purpose', value: 'purpose' },
    { text: 'Type', value: 'type' },
    { text: 'Source', value: 'source' },
    { text: 'Amount', value: 'amount' },
    { text: 'Actions', value: 'actions' },
];
</script>

<template>
    <AdminLayout>
        <div>
            <div class="flex justify-between items-center">
                <Link href="/dashboard/hotel-invoice" class="mb-4 text-white bg-cyan-950 hover:bg-blue-700 font-medium rounded-lg px-4 py-2 flex justify-center items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75 3 12m0 0 3.75-3.75M3 12h18" />
                    </svg>

                    Go Back
                </Link>
                <div>
                    <div class="flex items-center gap-4">
                        <button @click="openModal" class="mb-4 text-white bg-cyan-950 hover:bg-blue-700 font-medium rounded-lg px-4 py-2 flex justify-center items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 3.75H6.912a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859M12 3v8.25m0 0-3-3m3 3 3-3" />
                            </svg>
                            Add Adjustment
                        </button>
                        <Link href="/dashboard/hotel-invoice/all-invoices" class="mb-4 text-white bg-cyan-950 hover:bg-blue-700 font-medium rounded-lg px-4 py-2 flex justify-center items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 14.25 6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185ZM9.75 9h.008v.008H9.75V9Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm4.125 4.5h.008v.008h-.008V13.5Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                            All Invoices
                        </Link>
                        <Link href="/dashboard/hotel-invoice/create-invoice" class="mb-4 text-white bg-cyan-950 hover:bg-blue-700 font-medium rounded-lg px-4 py-2 flex justify-center items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                            </svg>
                            Create Invoice
                        </Link>
                        <Link href="/dashboard/hotel-invoice/eligible-invoices-for-update" class="mb-4 text-white bg-cyan-950 hover:bg-blue-700 font-medium rounded-lg px-4 py-2 flex justify-center items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 0 0-3.7-3.7 48.678 48.678 0 0 0-7.324 0 4.006 4.006 0 0 0-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 0 0 3.7 3.7 48.656 48.656 0 0 0 7.324 0 4.006 4.006 0 0 0 3.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3-3 3" />
                            </svg>
                            Update Invoice
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <Dialog :open="isModalOpen" @close="closeModal" class="fixed z-50 inset-0 overflow-y-auto" aria-hidden="false">
                <div class="flex items-center justify-center min-h-screen p-4 text-center">
                    <DialogOverlay class="fixed inset-0 bg-black opacity-30" />
                    <div class="relative bg-white w-full max-w-lg p-6 rounded-xl shadow-xl z-50">
                        <DialogTitle class="text-xl font-semibold mb-4">
                            {{ isEditMode ? 'Edit Adjustment' : 'Add Adjustment' }}
                        </DialogTitle>

                        <form @submit.prevent="handleSubmit" class="space-y-3">
                            <div class="w-full">
                                <label class="block text-sm font-medium">Month & Year</label>
                                <div class=" w-full flex gap-2">
                                    <!-- Month -->
                                    <select v-model="selectedMonth" class="border p-2 rounded w-[50%]">
                                        <option disabled value="">Month</option>
                                        <option v-for="m in 12" :key="m" :value="m">
                                            {{ new Date(2000, m - 1).toLocaleString("default", { month: "long" }) }}
                                        </option>
                                    </select>

                                    <!-- Year -->
                                    <select v-model="selectedYear" class="border p-2 rounded w-[50%]">
                                        <option disabled value="">Year</option>
                                        <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                                    </select>
                                </div>

                                <!-- Validation -->
                                <div v-if="data.errors.month" class="text-red-500 text-sm">
                                    {{ data.errors.month }}
                                </div>
                            </div>


                            <div>
                                <label for="purpose" class="block text-sm font-medium">Purpose</label>
                                <input v-model="data.purpose" type="text" class="w-full border p-2 rounded" />
                                <div v-if="data.errors.purpose" class="text-red-500 text-sm">{{ data.errors.purpose }}</div>
                            </div>
                            <div>
                                <Combobox v-model="data.type">
                                    <div class="relative">
                                        <label  class="block text-sm font-medium" >
                                            Select Type
                                        </label>

                                        <div
                                            class="relative w-full overflow-hidden rounded-lg border border-gray-400 bg-white text-left shadow-sm focus-within:border-blue-600 focus-within:ring-1 focus-within:ring-blue-600"
                                        >
                                            <ComboboxInput
                                                class="peer w-full border-none px-3 pt-4 pb-2 text-sm leading-5 text-gray-900 focus:ring-0"
                                                :displayValue="(type) => type || ''"
                                                placeholder=" "
                                            />
                                            <ComboboxButton class="absolute inset-y-0 right-0 flex items-center pr-2">
                                                <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                                            </ComboboxButton>
                                        </div>

                                        <TransitionRoot
                                            leave="transition ease-in duration-100"
                                            leaveFrom="opacity-100"
                                            leaveTo="opacity-0"
                                        >
                                            <ComboboxOptions
                                                class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm"
                                            >
                                                <ComboboxOption value="Debit" v-slot="{ selected, active }">
                                                    <li
                                                        class="relative cursor-default select-none py-2 pl-10 pr-4"
                                                        :class="{ 'bg-blue-600 text-white': active, 'text-gray-900': !active }"
                                                    >
                                                        <span :class="{ 'font-medium': selected, 'font-normal': !selected }">Debit</span>
                                                        <span
                                                            v-if="selected"
                                                            class="absolute inset-y-0 left-0 flex items-center pl-3"
                                                            :class="{ 'text-white': active, 'text-blue-600': !active }"
                                                        >
                                                            <CheckIcon class="h-5 w-5" aria-hidden="true" />
                                                          </span>
                                                    </li>
                                                </ComboboxOption>

                                                <ComboboxOption value="Credit" v-slot="{ selected, active }">
                                                    <li
                                                        class="relative cursor-default select-none py-2 pl-10 pr-4"
                                                        :class="{ 'bg-blue-600 text-white': active, 'text-gray-900': !active }"
                                                    >
                                                        <span :class="{ 'font-medium': selected, 'font-normal': !selected }">Credit</span>
                                                        <span
                                                            v-if="selected"
                                                            class="absolute inset-y-0 left-0 flex items-center pl-3"
                                                            :class="{ 'text-white': active, 'text-blue-600': !active }"
                                                        >
                                                            <CheckIcon class="h-5 w-5" aria-hidden="true" />
                                                          </span>
                                                    </li>
                                                </ComboboxOption>
                                            </ComboboxOptions>
                                        </TransitionRoot>
                                    </div>
                                </Combobox>
                                <div v-if="data.errors.type" class="text-red-500 text-sm pt-2">{{ data.errors.type }}</div>
                            </div>
                            <div>
                                <Combobox v-model="data.source">
                                    <div class="relative">
                                        <label class="block text-sm font-medium">Sources</label>

                                        <div
                                            class="relative w-full overflow-hidden rounded-lg border border-gray-400 bg-white text-left shadow-sm focus-within:border-blue-600 focus-within:ring-1 focus-within:ring-blue-600"
                                        >
                                            <ComboboxInput
                                                class="peer w-full border-none px-3 pt-4 pb-2 text-sm leading-5 text-gray-900 focus:ring-0"
                                                :displayValue="(source) => source || ''"
                                                placeholder=" "
                                            />
                                            <ComboboxButton class="absolute inset-y-0 right-0 flex items-center pr-2">
                                                <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                                            </ComboboxButton>
                                        </div>

                                        <TransitionRoot
                                            leave="transition ease-in duration-100"
                                            leaveFrom="opacity-100"
                                            leaveTo="opacity-0"
                                        >
                                            <ComboboxOptions
                                                class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm"
                                            >
                                                <!-- Placeholder -->
                                                <ComboboxOption value="" disabled v-slot="{ active }">
                                                    <li
                                                        class="relative cursor-default select-none py-2 pl-10 pr-4 text-gray-400"
                                                        :class="{ 'bg-gray-100': active }"
                                                    >
                                                        Please Select
                                                    </li>
                                                </ComboboxOption>

                                                <!-- Dynamic sources -->
                                                <ComboboxOption
                                                    v-for="source in sources.filter(s => s.source !== 'Expedia')"
                                                    :key="source.id"
                                                    :value="source.source"
                                                    v-slot="{ selected, active }"
                                                >
                                                    <li
                                                        class="relative cursor-default select-none py-2 pl-10 pr-4"
                                                        :class="{ 'bg-blue-600 text-white': active, 'text-gray-900': !active }"
                                                    >
                                                        <span :class="{ 'font-medium': selected, 'font-normal': !selected }">
                                                          {{ source.source }}
                                                        </span>
                                                                                                    <span v-if="selected" class="absolute inset-y-0 left-0 flex items-center pl-3 text-white">
                                                          <CheckIcon class="h-5 w-5" aria-hidden="true" />
                                                        </span>
                                                    </li>
                                                </ComboboxOption>

                                                <!-- Optional static sources -->
                                                <ComboboxOption value="Combined" v-slot="{ selected, active }">
                                                    <li
                                                        class="relative cursor-default select-none py-2 pl-10 pr-4"
                                                        :class="{ 'bg-blue-600 text-white': active, 'text-gray-900': !active }"
                                                    >
                                                        <span :class="{ 'font-medium': selected, 'font-normal': !selected }">Combined</span>
                                                        <span v-if="selected" class="absolute inset-y-0 left-0 flex items-center pl-3 text-white">
              <CheckIcon class="h-5 w-5" aria-hidden="true" />
            </span>
                                                    </li>
                                                </ComboboxOption>
                                                <ComboboxOption value="expediaCollects" v-slot="{ selected, active }">
                                                    <li
                                                        class="relative cursor-default select-none py-2 pl-10 pr-4"
                                                        :class="{ 'bg-blue-600 text-white': active, 'text-gray-900': !active }"
                                                    >
                                                        <span :class="{ 'font-medium': selected, 'font-normal': !selected }">Expedia Collects</span>
                                                        <span v-if="selected" class="absolute inset-y-0 left-0 flex items-center pl-3 text-white">
              <CheckIcon class="h-5 w-5" aria-hidden="true" />
            </span>
                                                    </li>
                                                </ComboboxOption>
                                                <ComboboxOption value="expediaHotelCollects" v-slot="{ selected, active }">
                                                    <li
                                                        class="relative cursor-default select-none py-2 pl-10 pr-4"
                                                        :class="{ 'bg-blue-600 text-white': active, 'text-gray-900': !active }"
                                                    >
                                                        <span :class="{ 'font-medium': selected, 'font-normal': !selected }">Expedia Hotel Collects</span>
                                                        <span v-if="selected" class="absolute inset-y-0 left-0 flex items-center pl-3 text-white">
              <CheckIcon class="h-5 w-5" aria-hidden="true" />
            </span>
                                                    </li>
                                                </ComboboxOption>
                                            </ComboboxOptions>
                                        </TransitionRoot>
                                    </div>
                                </Combobox>
                            </div>

                            <div>
                                <label for="amount" class="block text-sm font-medium">Amount</label>
                                <input v-model="data.amount" type="text" class="w-full border p-2 rounded" />
                                <div v-if="data.errors.amount" class="text-red-500 text-sm">{{ data.errors.amount }}</div>
                            </div>
                            <div class="flex justify-end space-x-2 mt-4">
                                <button type="button" @click="closeModal" class="px-4 py-2 bg-red-600 rounded hover:bg-red-700 text-sm text-white">Cancel</button>
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-cyan-950 text-white rounded hover:bg-blue-700 text-sm flex items-center justify-center min-w-[120px]"
                                    :disabled="isSubmitting"
                                >
                                    <svg v-if="isSubmitting" class="animate-spin h-4 w-4 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                    </svg>
                                    {{ isSubmitting ? (isEditMode ? 'Updating...' : 'Submitting...') : (isEditMode ? 'Update' : 'Submit ') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </Dialog>

            <!-- Table -->
            <EasyDataTable
                buttons-pagination
                :headers="tableHeaders"
                :items="userData"
                :rows-per-page="5"
                table-class-name="customize-table"
                show-index
            >
                <template #item-month="item">
                    {{ formatMonthYear(item.month) }}
                </template>
                <template #item-actions="item">
                    <div class="flex gap-2">
                        <button @click="handleEdit(item)" class="bg-yellow-400 text-white px-2 py-1 rounded text-sm hover:bg-yellow-500">
                            Edit
                        </button>
                        <button @click="handleDelete(item.id)" class="bg-red-500 text-white px-2 py-1 rounded text-sm hover:bg-red-600">
                            Delete
                        </button>
                    </div>
                </template>
            </EasyDataTable>
        </div>
    </AdminLayout>
</template>

<style scoped>
::v-deep(.customize-table) {
    --easy-table-header-font-size: 16px;
    --easy-table-body-row-font-size: 14px;
    --easy-table-header-font-color: #111827;
    --easy-table-body-row-font-color: #374151;
    --easy-table-border: 1px solid #e5e7eb;
}
::v-deep(.customize-table thead th:nth-child(3)),
::v-deep(.customize-table tbody td:nth-child(3)) {
    max-width: 200px;
    word-wrap: break-word;
    word-break: break-word;
    white-space: normal;
}
</style>
