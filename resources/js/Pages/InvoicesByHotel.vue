<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {usePage, Link, useForm, router} from "@inertiajs/vue3";
import {computed, ref, watch} from 'vue';
import Swal from "sweetalert2";
import dayjs from 'dayjs';
import { route } from 'ziggy-js';
import customParseFormat from 'dayjs/plugin/customParseFormat';
import {debounce} from "lodash-es";
const id = route().params.id;
dayjs.extend(customParseFormat);
const invoicesByHotel = ref(usePage().props.invoicesByHotel);
const sources = ref(usePage().props.sources);
const invoicesDataArray = computed(() => invoicesByHotel.value.success);
const tableHeaders = [
    { text: 'Invoice No', value: 'inv_no' },
    { text: 'Invoice Date', value: 'inv_date' },
    { text: 'Hotel Name', value: 'hotel.hotelName' },
    { text: 'Guest Name', value: 'reservation.guest_name' },
    { text: 'Check In', value: 'reservation.check_in' },
    { text: 'Check Out', value: 'reservation.check_out' },
    { text: 'Total Amount', value: 'total_amount' },
    { text: 'Actions', value: 'actions' },
];
const searchValue = ref('');
const reservationData = useForm({
    hotel_id: '',
})
const fetchUsers = () => {
    router.reload({
        only: ['invoicesByHotel'],
        onSuccess: () => {
            invoicesByHotel.value = usePage().props.invoicesByHotel ;
        }
    });
};
const handleViewInvoices=(id)=>{
    reservationData.hotel_id = id;
    console.log(reservationData);
    reservationData.post('/dashboard/hotel-invoice/invoices-by-hotel', {
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Invoice Created successfully',
                showConfirmButton: false,
                timer: 2000
            });
            // Reset reservation form fields
            reservationData.reset();
            fetchUsers();
        },
    });
}

const filters = ref({
    month: null,
    year: null,
    showAll: false
});
const years = [2021,2022, 2023, 2024, 2025, 2026,2027,2028,2029,2030,2031,2032,2033,2034,2035];

const fetchInvoices = debounce(() => {
    router.get(
        `/dashboard/hotel-invoice/all-invoices/${id}`,
        {
            search: searchValue.value,
            month: filters.value.month,
            year: filters.value.year,
            showAll: filters.value.showAll,
        },
        {
            preserveState: true,
            replace: true,
            onSuccess: () => {
                invoicesByHotel.value = usePage().props.invoicesByHotel;
            },
        }
    );
}, 400);
watch(searchValue,() => {
    fetchInvoices();
});

watch(filters, () => {
    fetchInvoices();
}, { deep: true });
const monthSelections = ref({});
watch(() => invoicesByHotel.value, (val) => {
    if (val?.data) {
        val.data.forEach(month => {
            if (!(month.month in monthSelections.value)) {
                monthSelections.value[month.month] = "" // default = Please Select
            }
        })
    }
}, { immediate: true })

const handleInvoiceDownload = async (monthData) => {
    console.log(monthData);
    const downloadType = monthSelections.value[monthData.month] || "";
    const firstInvoice = monthData.data[0];
    const hotel = firstInvoice?.hotelName || firstInvoice?.hotel?.hotelName || "";
    const hotelAddress = firstInvoice?.hotelAddress || firstInvoice?.hotel?.hotelAddress || "";
    try {
        const response = await fetch(route('hotelInvoice.pdf'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({
                month: monthData.month,
                downloadType:downloadType,
                invoices: monthData.data,
                hotel,
                hotelAddress,
            }),

        });


        if (!response.ok) {
            throw new Error('Failed to fetch PDF');
        }

        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);

        const a = document.createElement('a');
        a.href = url;
        a.download = `${monthData.month} invoice.pdf`; // or dynamically set name from response headers
        document.body.appendChild(a);
        a.click();
        a.remove();

        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Download failed:', error);
    }


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
const  handleDelete=(id)=>{
    console.log(id);
    Swal.fire({
        title: 'Are you sure?',
        text: "This Invoice will be deleted permanently!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('deleteInvoice', id), {
                onSuccess: () => {
                    Toast.fire({
                        icon: "warning",
                        title: "Invoice Deleted successfully"
                    });
                    fetchInvoices();
                },
                onError: (errors) => {
                    console.error(errors)
                }
            })

        }
    });
}
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
                    <Link href="/dashboard/hotel-invoice/invoice-adjustment" class="mb-4 text-white bg-cyan-950 hover:bg-blue-700 font-medium rounded-lg px-4 py-2 flex justify-center items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                        </svg>
                        Invoice Adjustment
                    </Link>
                </div>
            </div>

            <div class="flex justify-between items-center gap-2 mb-2">
                <div class="bg-white shadow-md px-4 py-2 rounded-lg  w-full ">
                    <h1 v-if="!invoicesDataArray" class="text-red-600 text-2xl font-bold py-0.5">No Data Found</h1>
                    <h1 v-else class="text-cyan-950 text-2xl font-bold py-0.5">{{invoicesByHotel.hotel}}</h1>
                </div>
                <div class="relative  shadow-md  w-full ">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="search" v-model="searchValue" id="default-search" class="block w-full p-4 ps-10 text-sm text-gray-900  rounded-lg bg-white border-none focus:ring-none focus:border-none" placeholder="Search anything ..." />
                </div>
                <div class="flex gap-2 px-4 py-2 w-full shadow-md bg-white rounded-lg items-center justify-between">
                    <!-- Month Filter -->
                    <select v-model="filters.month" class=" rounded outline-none border-none focus:outline-none focus:border-none py-1.5">
                        <option value=null>Select Month</option>
                        <option v-for="m in 12" :value="String(m).padStart(2, '0')">
                            {{ dayjs(`2025-${String(m).padStart(2, '0')}-01`).format('MMMM') }}
                        </option>
                    </select>

                    <!-- Year Filter -->
                    <select v-model="filters.year" class=" rounded outline-none border-none focus:outline-none focus:border-none py-1.5">
                        <option value=null>Select Year</option>
                        <option v-for="y in years" :value="y">{{ y }}</option>
                    </select>

                    <!-- All Invoices -->
                    <label class="flex gap-2 items-center">
                        <input type="checkbox" v-model="filters.showAll" />
                        Show All
                    </label>
                </div>

            </div>
            <div v-if="!invoicesDataArray">
                <div class="bg-white shadow-md px-4 py-2 rounded-lg">
                    <h1 class="text-red-600 text-center text-xl font-bold py-0.5">No Data Found !!!</h1>
                </div>
            </div>
            <div v-else v-for="monthData in invoicesByHotel.data" :key="monthData.month" class="mb-4">
                <div class="bg-white shadow-md px-4 py-2 rounded-t-lg flex items-center justify-between">
                    <h1 class="text-cyan-950 text-xl font-bold py-0.5">{{monthData.month}}</h1>
                    <div>
                        <div class="text-sm font-semibold">
                            <form @submit.prevent="handleInvoiceDownload(monthData)" class="flex items-center gap-2  justify-center">
                                <select v-model="monthSelections[monthData.month]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                    <option disabled value="">Please Select</option>
                                    <option value="Combined">Combined</option>
                                    <option value="expediaCollects">Expedia Collects</option>
                                    <option value="expediaHotelCollects">Expedia Hotel Collects</option>
                                    <option
                                        v-for="source in sources.filter(s => s.source !== 'Expedia')"
                                        :key="source.id"
                                        :value="source.source"
                                    >
                                        {{ source.source }}
                                    </option>
                                </select>
                                <button
                                    type="submit"
                                    :disabled="!monthSelections[monthData.month]"
                                    :class="[
                                        'px-4 py-2 rounded flex justify-center items-center gap-2',
                                        monthSelections[monthData.month]
                                            ? 'bg-cyan-950 text-white cursor-pointer hover:bg-blue-700'
                                            : 'bg-gray-400 text-gray-200 cursor-not-allowed'
                                    ]"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12
                                         16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                    Download
                                </button>

                            </form>
                        </div>
                    </div>
                </div>
                <EasyDataTable
                    :headers="tableHeaders"
                    :items="monthData.data"
                    :search-value="searchValue"
                    table-class-name="customize-table"
                    show-index
                >
                    <template #item-actions="item">
                        <div class="flex gap-2">
<!--                            <button type="submit" class=" px-3 py-1 bg-cyan-950  text-white rounded flex justify-center items-center gap-2">-->
<!--                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">-->
<!--                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />-->
<!--                                </svg>-->
<!--                                Single Invoice-->
<!--                            </button>-->
                            <button @click="handleDelete(item.id)" class=" text-white  rounded text-sm ">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-red-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </div>
                    </template>
                </EasyDataTable>
            </div>

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
    padding-bottom : 12px !important;
    background-color: white !important;
    border-bottom-left-radius: 0.5rem !important;
    border-bottom-right-radius: 0.5rem !important;
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
::v-deep(.vue3-easy-data-table__footer) {
    display: none !important;
}

</style>
