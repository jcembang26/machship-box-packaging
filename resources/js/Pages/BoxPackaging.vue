<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import VueTableLite from "vue3-table-lite";
import { toast } from 'vue3-toastify';

import SvgIcon from "vue3-icon";
import { faTrash } from "@fortawesome/free-solid-svg-icons";
import 'vue3-toastify/dist/index.css';

import { reactive, ref } from 'vue';

const form = useForm({
    name: '',
    len: null,
    height: null,
    width: null,
    weight: null,
    qty: null
});

let remainingProducts = ref(10);
let boxesCreated = ref([])

const table = reactive({
    isLoading: false,
    columns: [
    {
        label: "ID",
        field: "id",
        width: "3%",
        sortable: true,
        isKey: true,
    },
    {
        label: "Name",
        field: "name",
        width: "20%",
        sortable: true,
    },
    {
        label: "Length (cm)",
        field: "length",
        width: "10%",
        sortable: false,
    },
    {
        label: "Height (cm)",
        field: "height",
        width: "10%",
        sortable: false,
    },
    {
        label: "Width (cm)",
        field: "width",
        width: "10%",
        sortable: false,
    },
    {
        label: "Weight (kg)",
        field: "weight",
        width: "10%",
        sortable: true,
    },
    {
        label: "Quantity",
        field: "quantity",
        width: "10%",
        sortable: true,
    },
    {
      label: "Actions",
      field: "action",
      width: "10%",
      sortable: false,
    },
    ],
    rows: [],
    totalRecordCount: 0,
    sortable: {
        order: "id",
        sort: "asc",
    },
});

function addProduct() {

    if(form.name.length === 0 || (form.len.length === 0 && form.width.length === 0 && form.height.length === 0) || table.rows.length >= 10){
        return false
    }

    const lastId = table.rows.reduce((acc, curr) => curr.id, null) ?? 0;

    if(remainingProducts.value > 0){
        remainingProducts.value = remainingProducts.value - 1
    }

    table.rows.push({
        id: lastId + 1,
        name: form.name,
        length: form.len,
        width: form.width,
        height: form.height,
        weight: form.weight,
        quantity: form.qty,
    })

    form.reset()
}

function delProduct(id = 0) {

    if(!id || id === 0){
        return false
    }

    if(remainingProducts.value < 10){
        remainingProducts.value = remainingProducts.value + 1
    }

    table.rows = table.rows.filter(el => el.id !== id)
}

async function packProducts() {
    try {

        const config = {
            headers: {
                'content-type': 'multipart/form-data'
            }
        }
        
        const res = await axios.post('/api/pack-products', {products: table.rows}, config)
      
        if(res.status === 200){
          boxesCreated.value = res.data.map(el => {
      
              const { name, items } = el
      
              return {
                  name,
                  totalItems: items.length,
                  totalWeight: items.reduce((accumulator, currentValue) => {
                                  return accumulator + currentValue.weight;
                              }, 0)
              }
          })
        }
    } catch (error) {
        toast(error.response.data.message ?? 'Something went wrong with this action!', {
            "type": "error"
        });
    }

}

function resetPacking(){
    table.rows = []
    form.reset()
    boxesCreated.value = []
}

</script>

<template>
    <Head title="Pack Products" />

    <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent class="space-y-6" style="padding: 50px;">
                            <div>

                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mr-1"
                                    v-model="form.name"
                                    placeholder="Name"
                                    required
                                    autofocus
                                />

                                <InputError class="mt-2" :message="form.errors.name" />
                                
                                <TextInput
                                    id="len"
                                    type="text"
                                    class="mt-1"
                                    v-model="form.len"
                                    placeholder="Length (cm)"
                                    required
                                    autofocus
                                />

                                <InputError class="mt-2" :message="form.errors.name" />

                                <TextInput
                                    id="width"
                                    type="text"
                                    class="mt-1 mr-1 ml-1"
                                    v-model="form.width"
                                    placeholder="Width (cm)"
                                    required
                                    autofocus
                                />

                                <InputError class="mt-2" :message="form.errors.name" />

                                <TextInput
                                    id="height"
                                    type="text"
                                    class="mt-1"
                                    v-model="form.height"
                                    placeholder="Height (cm)"
                                    required
                                    autofocus
                                />

                                <InputError class="mt-2" :message="form.errors.name" />

                                <TextInput
                                    id="weight"
                                    type="text"
                                    class="mt-1 ml-1"
                                    v-model="form.weight"
                                    placeholder="Kg"
                                    required
                                    autofocus
                                />

                                <InputError class="mt-2" :message="form.errors.name" />

                                <TextInput
                                    id="qty"
                                    type="text"
                                    class="mt-1 ml-1"
                                    v-model="form.qty"
                                    placeholder="Quantity"
                                    required
                                    autofocus
                                />

                                <InputError class="mt-2" :message="form.errors.name" />
                                
                                <PrimaryButton @click="addProduct()" class="ml-1" :disabled="table.rows.length >= 10">Add Product {{ `(${remainingProducts})` }}</PrimaryButton>
                            </div>
                        </form>

                        
                        <VueTableLite
                            :is-loading="table.isLoading"
                            :columns="table.columns"
                            :rows="table.rows"
                            :total="table.totalRecordCount"
                            :sortable="table.sortable"
                            :is-hide-paging="true"
                            :is-slot-mode="true"
                            @do-search="doSearch"
                            @is-finished="tableLoadingFinish"
                        >
                            <template v-slot:action="data">
                                <div style="display: flex; justify-content: space-evenly;">
                                    <svg-icon class="clickable" :fa-icon="faTrash" :size="18" flip="horizontal" @click="delProduct(data.value.id)" ></svg-icon>
                                </div>
                            </template>
                        </VueTableLite>


                        <div v-if="table.rows.length > 0" class="mt-2 mb-2">
                            <PrimaryButton @click="packProducts()" class="ml-1">Pack Products</PrimaryButton>
                        </div>
                        
                        <div class="flex">
                            <div v-for="box in boxesCreated" class="box px-2 mx-3">
                                <span>Name: {{ box.name  }}</span>
                                <br/>
                                <span># of items: {{ box.totalItems  }}</span>
                                <br/>
                                <span>Total weight: {{ box.totalWeight  }}</span>
                                <br/>
                            </div>
                        </div>

                        <div v-if="boxesCreated.length > 0" class="mt-2 mb-2">
                            <PrimaryButton @click="resetPacking()" class="ml-1">Reset</PrimaryButton>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.bg-dots-darker {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}
@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
}

.box {
    width: 200px;
    height: 150px;
    border: 1px solid gray;
    border-radius: 5px;
}
</style>
