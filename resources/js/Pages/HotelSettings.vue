<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { router, useForm, usePage,Link } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { ref } from "vue";
import { Dialog, DialogOverlay, DialogTitle } from "@headlessui/vue";
// State
const userData = ref(usePage().props.hotels);
const isModalOpen = ref(false);
const isEditMode = ref(false);
const isSubmitting = ref(false);
let editingUserId = null;
// Modal Control
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
    // Ensure focus goes to a safe visible element (like the Add button)
    // setTimeout(() => {
    //     const addButton = document.getElementById('add-user-btn');
    //     if (addButton) addButton.focus();
    // }, 100);
};
// Form
const data = useForm({
    hotelName: '',
    hotelAddress: '',
    commissionType: '',
    expediaCollectsCommission: '',
    hotelCollectsCommission: '',
});
// Refresh User List
const fetchUsers = () => {
    router.reload({
        only: ['hotels'],
        onSuccess: () => {
            userData.value = usePage().props.hotels;
        }
    });
};

// Add or Update User
const handleSubmit = () => {
    isSubmitting.value = true;
    if (data.commissionType === 'zero') {
        data.expediaCollectsCommission = null;
        data.hotelCollectsCommission = null;
    }
    if (isEditMode.value) {
        data.put(`/dashboard/settings/update-hotel/${editingUserId}`, {
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Hotel updated successfully',
                    showConfirmButton: false,
                    timer: 1000
                });
                closeModal();
                fetchUsers();
            },
            onFinish: () => isSubmitting.value = false
        });
    } else {
        data.post('/dashboard/settings/add-hotel', {
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Hotel added successfully',
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

// Prepare Edit
const handleEdit = (id,hotelName,hotelAddress,commissionType,expediaCollectsCommission,hotelCollectsCommission) => {
    editingUserId = id;
    isEditMode.value = true;
    data.hotelName = hotelName;
    data.hotelAddress = hotelAddress;
    data.commissionType = commissionType;
    data.expediaCollectsCommission = expediaCollectsCommission;
    data.hotelCollectsCommission = hotelCollectsCommission;
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
        text: "This Hotel will be deleted permanently!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.get(`/dashboard/settings/delete-hotel/${id}`)
            Toast.fire({
                icon: "warning",
                title: "Hotel Deleted successfully"
            });
        }
    });
};

// Table Headers
const tableHeaders = [
    { text: 'Name', value: 'hotelName' },
    { text: 'Address', value: 'hotelAddress' },
    { text: 'Commission Type', value: 'commissionType' },
    { text: 'Expedia Collects', value: 'expediaCollectsCommission' },
    { text: 'Hotel Collects', value: 'hotelCollectsCommission' },
    { text: 'Actions', value: 'actions' },
];
</script>

<template>
    <AdminLayout>
        <div>
            <div class="flex justify-between items-center">
                <Link href="/dashboard/settings" class="mb-4 text-white bg-cyan-950 hover:bg-blue-700 font-medium rounded-lg px-4 py-2 flex justify-center items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75 3 12m0 0 3.75-3.75M3 12h18" />
                    </svg>

                    Go Back
                </Link>

                <button @click="openModal" class="mb-4 text-white bg-cyan-950 hover:bg-blue-700 font-medium rounded-lg px-4 py-2 flex justify-center items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke-width="1.5"
                         stroke="currentColor"
                         class="size-6">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                    </svg>

                    Add Hotel
                </button>
            </div>

            <!-- Modal -->
            <Dialog :open="isModalOpen" @close="closeModal" class="fixed z-50 inset-0 overflow-y-auto" aria-hidden="false">
                <div class="flex items-center justify-center min-h-screen p-4 text-center">
                    <DialogOverlay class="fixed inset-0 bg-black opacity-30" />
                    <div class="relative bg-white w-full max-w-lg p-6 rounded-xl shadow-xl z-50">
                        <DialogTitle class="text-xl font-semibold mb-4">
                            {{ isEditMode ? 'Edit Hotel Info' : 'Add Hotel Info' }}
                        </DialogTitle>

                        <form @submit.prevent="handleSubmit" class="space-y-3">
                            <div>
                                <label for="hotelName" class="block text-sm font-medium">Hotel Name</label>
                                <input v-model="data.hotelName" type="text" class="w-full border p-2 rounded" />
                                <div v-if="data.errors.hotelName" class="text-red-500 text-sm">{{ data.errors.hotelName }}</div>
                            </div>
                            <div>
                                <label for="hotelAddress" class="block text-sm font-medium">Hotel Address</label>
                                <input v-model="data.hotelAddress" type="text" class="w-full border p-2 rounded" />
                                <div v-if="data.errors.hotelAddress" class="text-red-500 text-sm">{{ data.errors.hotelAddress }}</div>
                            </div>
                            <div>
                                <ul class="items-center w-full text-sm font-medium  bg-white border border-black rounded-lg sm:flex">
                                    <li class="w-full border-b border-black sm:border-b-0 sm:border-r 0">
                                        <div class="flex items-center ps-3">
                                            <input id="horizontal-list-radio-license"
                                                   type="radio"
                                                   value="percent"
                                                   v-model="data.commissionType"
                                                   name="commissionType"
                                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600">
                                            <label for="horizontal-list-radio-license" class="w-full py-3 ms-2 text-sm font-medium  ">Percent Based Commission</label>
                                        </div>
                                    </li>
                                    <li class="w-full border-b border-black sm:border-b-0">
                                        <div class="flex items-center ps-3">
                                            <input id="horizontal-list-radio-id"
                                                   type="radio"
                                                   value="zero"
                                                   v-model="data.commissionType"
                                                   name="commissionType" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600">
                                            <label for="horizontal-list-radio-id" class="w-full py-3 ms-2 text-sm font-medium  ">Zero Commission</label>
                                        </div>
                                    </li>

                                </ul>
                                <div v-if="data.errors.commissionType" class="text-red-500 text-sm">{{ data.errors.commissionType }}</div>
                            </div>
                            <div v-if="data.commissionType === 'percent'">
                                <label for="expediaCollectsCommission" class="block text-sm font-medium">Expedia Collects Commission</label>
                                <input v-model="data.expediaCollectsCommission" type="text" class="w-full border p-2 rounded" />
                                <div v-if="data.errors.expediaCollectsCommission" class="text-red-500 text-sm">{{ data.errors.expediaCollectsCommission }}</div>
                            </div>
                            <div v-if="data.commissionType === 'percent'">
                                <label for="hotelCollectsCommission" class="block text-sm font-medium">Hotel Collects Commission</label>
                                <input v-model="data.hotelCollectsCommission" type="text" class="w-full border p-2 rounded" />
                                <div v-if="data.errors.hotelCollectsCommission" class="text-red-500 text-sm">{{ data.errors.hotelCollectsCommission }}</div>
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
                <!-- Custom column rendering -->
                <template #item-expediaCollectsCommission="{ expediaCollectsCommission }">
                    {{ expediaCollectsCommission ?? 'N/A' }}
                </template>

                <template #item-hotelCollectsCommission="{ hotelCollectsCommission }">
                    {{ hotelCollectsCommission ?? 'N/A' }}
                </template>
                <template #item-actions="{ id,hotelName,hotelAddress,commissionType,expediaCollectsCommission,hotelCollectsCommission }">
                    <div class="flex gap-2">
                        <button @click="handleEdit(id,hotelName,hotelAddress,commissionType,expediaCollectsCommission,hotelCollectsCommission)" class="bg-yellow-400 text-white px-2 py-1 rounded text-sm hover:bg-yellow-500">
                            Edit
                        </button>
                        <button @click="handleDelete(id)" class="bg-red-500 text-white px-2 py-1 rounded text-sm hover:bg-red-600">
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
