<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {usePage, Link} from "@inertiajs/vue3";
import { ref} from 'vue';
const searchValue = ref('');
const hotelNames = ref(usePage().props.hotelNames);
const tableHeaders = [
    { text: 'Hotel', value: 'hotelName' },
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
                <div class="flex items-center gap-4">
                    <Link href="/dashboard/reservations/all-reservations" class="mb-4 text-white bg-cyan-950 hover:bg-blue-700 font-medium rounded-lg px-4 py-2 flex justify-center items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                        </svg>
                        All Reservations
                    </Link>
                </div>
            </div>

            <div class="flex justify-between items-center gap-2 mb-2">
                <div class="bg-white shadow-md px-4 py-2 rounded-lg w-1/2">
                    <h1 class="text-cyan-950 text-2xl font-bold py-0.5">All Hotel Invoices</h1>
                </div>
                <div class="relative  shadow-md w-1/2">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="search" v-model="searchValue" id="default-search" class="block w-full p-4 ps-10 text-sm text-gray-900  rounded-lg bg-white border-none focus:ring-none focus:border-none" placeholder="Search anything..." />
                </div>
            </div>
            <EasyDataTable
                buttons-pagination
                :headers="tableHeaders"
                :items="hotelNames"
                :search-value="searchValue"
                :rows-per-page="100"
                table-class-name="customize-table"
                show-index
            >
                <template #item-actions="item">
                    <div class="flex gap-2">
                        <Link  :href="`/dashboard/hotel-invoice/all-invoices/${item.id}`" type="button" class=" text-white  rounded text-sm px-3 py-1 bg-cyan-950 hover:bg-blue-700">
                            View Invoices
                        </Link>
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
::v-deep(.vue3-easy-data-table__main) {
    min-height: auto !important;
}
</style>
