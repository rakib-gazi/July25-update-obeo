<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { router, useForm, usePage,Link } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { ref } from "vue";
import { Dialog, DialogOverlay, DialogTitle } from "@headlessui/vue";
// State
const userData = ref(usePage().props.statuses);
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
};
// Form
const data = useForm({
    status: '',
});
// Refresh User List
const fetchUsers = () => {
    router.reload({
        only: ['statuses'],
        onSuccess: () => {
            userData.value = usePage().props.statuses ;
        }
    });
};

// Add or Update User
const handleSubmit = () => {
    isSubmitting.value = true;
    if (isEditMode.value) {
        data.put(`/dashboard/settings/update-reservation-status/${editingUserId}`, {
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Reservation Status updated successfully',
                    showConfirmButton: false,
                    timer: 1000
                });
                closeModal();
                fetchUsers();
            },
            onFinish: () => isSubmitting.value = false
        });
    } else {
        data.post('/dashboard/settings/add-reservation-status', {
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Reservation Status added successfully',
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
const handleEdit = (id,status) => {
    editingUserId = id;
    isEditMode.value = true;
    data.status = status;
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
        text: "This Reservation Status will be deleted permanently!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.get(`/dashboard/settings/delete-reservation-status/${id}`)
            Toast.fire({
                icon: "warning",
                title: "Reservation Status Deleted successfully"
            });
        }
    });
};

// Table Headers
const tableHeaders = [
    { text: 'Reservation Status', value: 'status' },
    { text: 'Actions', value: 'actions' },
];

const disabledStatuses = ['Checked In', 'No show', 'Canceled', 'Charged'];

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
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                    </svg>
                    Add Reservation Status
                </button>
            </div>

            <!-- Modal -->
            <Dialog :open="isModalOpen" @close="closeModal" class="fixed z-50 inset-0 overflow-y-auto" aria-hidden="false">
                <div class="flex items-center justify-center min-h-screen p-4 text-center">
                    <DialogOverlay class="fixed inset-0 bg-black opacity-30" />
                    <div class="relative bg-white w-full max-w-lg p-6 rounded-xl shadow-xl z-50">
                        <DialogTitle class="text-xl font-semibold mb-4">
                            {{ isEditMode ? 'Edit Reservation Status' : 'Add Reservation Status' }}
                        </DialogTitle>

                        <form @submit.prevent="handleSubmit" class="space-y-3">
                            <div>
                                <label for="status" class="block text-sm font-medium">Reservation Status</label>
                                <input v-model="data.status" type="text" class="w-full border p-2 rounded" />
                                <div v-if="data.errors.status" class="text-red-500 text-sm">{{ data.errors.status }}</div>
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
                :rows-per-page="50"
                table-class-name="customize-table"
                show-index
            >
                <template #item-actions="{ id,status}">
                    <div class="flex gap-2">
                        <button
                            @click="handleEdit(id, status)"
                            :disabled="disabledStatuses.includes(status)"
                            class="px-2 py-1 rounded text-sm "
                            :class="disabledStatuses.includes(status)
                            ? 'text-blue-600 cursor-not-allowed'
                            : 'text-blue-600 hover:bg-blue-600 hover:text-white'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 ">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                        </button>
                        <button
                            @click="handleDelete(id)"
                            :disabled="disabledStatuses.includes(status)"
                            class="px-2 py-1 rounded text-sm "
                            :class="disabledStatuses.includes(status)
                            ? ' text-red-600 cursor-not-allowed'
                            : ' text-red-600 hover:bg-red-600 hover:text-white'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5  ">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
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
