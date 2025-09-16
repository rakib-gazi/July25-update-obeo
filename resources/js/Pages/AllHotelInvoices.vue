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
                    <Link href="/dashboard/hotel-invoice/invoice-adjustment" class="mb-4 text-white bg-cyan-950 hover:bg-blue-700 font-medium rounded-lg px-4 py-2 flex justify-center items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                        </svg>
                        Invoice Adjustment
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
