<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {Link, router, useForm, usePage} from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { ref } from "vue";
import { Dialog, DialogOverlay, DialogTitle } from "@headlessui/vue";
import logo from "../../images/logo.png";

// State
const userData = ref(usePage().props.users);
const isModalOpen = ref(false);
const isEditMode = ref(false);
const isSubmitting = ref(false);
let editingUserId = null;

// Form
const data = useForm({
    fullName: '',
    userName: '',
    phone: '',
    email: '',
    role: '',
    password: '',
});

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
    setTimeout(() => {
        const addButton = document.getElementById('add-user-btn');
        if (addButton) addButton.focus();
    }, 100);
};



// Refresh User List
const fetchUsers = () => {
    router.reload({
        only: ['users'],
        onSuccess: () => {
            userData.value = usePage().props.users;
        }
    });
};

// Add or Update User
const handleSubmit = () => {
    isSubmitting.value = true;
    if (isEditMode.value) {
        data.put(`/dashboard/users/${editingUserId}`, {
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    title: 'User updated successfully',
                    showConfirmButton: false,
                    timer: 1000
                });
                closeModal();
                fetchUsers();
            },
            onFinish: () => isSubmitting.value = false
        });
    } else {
        data.post('/dashboard/users', {
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    title: 'User added successfully',
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
const handleEdit = (id,fullName,userName,phone,email,role,password) => {
    editingUserId = id;
    isEditMode.value = true;
    data.fullName = fullName;
    data.userName = userName;
    data.phone = phone;
    data.email = email;
    data.role = role;
    data.password = password;
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
        text: "This user will be deleted permanently!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.get(`/dashboard/delete-user/${id}`)
            Toast.fire({
                icon: "warning",
                title: "User Deleted successfully"
            });
        }
    });
};

// Table Headers
const tableHeaders = [
    { text: 'Full Name', value: 'fullName' },
    { text: 'User Name', value: 'userName' },
    { text: 'Phone', value: 'phone' },
    { text: 'Email', value: 'email' },
    { text: 'Role', value: 'role' },
    { text: 'Actions', value: 'actions' },
];
</script>

<template>
    <AdminLayout>
        <div>

            <div class="flex justify-between items-center">
                <Link href="/dashboard" class="mb-4 text-white bg-cyan-950 hover:bg-blue-700 font-medium rounded-lg px-4 py-2 flex justify-center items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75 3 12m0 0 3.75-3.75M3 12h18" />
                    </svg>

                    Go Back
                </Link>

                <button @click="openModal" class="mb-4 text-white bg-cyan-950 hover:bg-blue-700 font-medium rounded-lg px-4 py-2 flex justify-center items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                    </svg>

                    Add User
                </button>
            </div>

            <!-- Modal -->
            <Dialog :open="isModalOpen" @close="closeModal" class="fixed z-50 inset-0 overflow-y-auto" aria-hidden="false">
                <div class="flex items-center justify-center min-h-screen p-4 text-center">
                    <DialogOverlay class="fixed inset-0 bg-black opacity-30" />
                    <div class="relative bg-white w-full max-w-lg p-6 rounded-xl shadow-xl z-50">
                        <DialogTitle class="text-xl font-semibold mb-4">
                            {{ isEditMode ? 'Edit User' : 'Add New User' }}
                        </DialogTitle>

                        <form @submit.prevent="handleSubmit" class="space-y-3">
                            <div>
                                <label for="fullName" class="block text-sm font-medium">Full Name</label>
                                <input v-model="data.fullName" type="text" class="w-full border p-2 rounded" />
                                <div v-if="data.errors.fullName" class="text-red-500 text-sm">{{ data.errors.fullName }}</div>
                            </div>

                            <div>
                                <label for="userName" class="block text-sm font-medium">User Name</label>
                                <input v-model="data.userName" type="text" class="w-full border p-2 rounded" />
                                <div v-if="data.errors.userName" class="text-red-500 text-sm">{{ data.errors.userName }}</div>
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium">Phone</label>
                                <input v-model="data.phone" type="text" class="w-full border p-2 rounded" />
                                <div v-if="data.errors.phone" class="text-red-500 text-sm">{{ data.errors.phone }}</div>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium">Email</label>
                                <input v-model="data.email" type="email" class="w-full border p-2 rounded" />
                                <div v-if="data.errors.email" class="text-red-500 text-sm">{{ data.errors.email }}</div>
                            </div>

                            <div>
                                <label for="role" class="block text-sm font-medium">Role</label>
                                <select v-model="data.role" class="w-full border p-2 rounded">
                                    <option disabled value="">Select Role</option>
                                    <option value="Admin">Admin</option>
                                    <option value="Moderator">Moderator</option>
                                </select>
                                <div v-if="data.errors.role" class="text-red-500 text-sm">{{ data.errors.role }}</div>
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium">Password</label>
                                <input v-model="data.password" type="password" class="w-full border p-2 rounded" />
                                <div v-if="data.errors.password" class="text-red-500 text-sm">{{ data.errors.password }}</div>
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
                                    {{ isSubmitting ? (isEditMode ? 'Updating...' : 'Adding...') : (isEditMode ? 'Update User' : 'Add User') }}
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
                <template #item-actions="{ id,fullName,userName,phone,email,role,password }">
                    <div class="flex gap-2">
                        <button @click="handleEdit(id,fullName,userName,phone,email,role,password)" class=" text-white  rounded text-sm ">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-blue-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                        </button>
                        <button @click="handleDelete(id)" class=" text-white  rounded text-sm ">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-red-500">
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
    --easy-table-body-row-font-size: 16px;
    --easy-table-header-font-color: #111827;
    --easy-table-body-row-font-color: #374151;
    --easy-table-border: 1px solid #e5e7eb;
}
</style>
