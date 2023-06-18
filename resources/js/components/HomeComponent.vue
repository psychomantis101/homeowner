<template>
    <div>
        <input type="file" @change="onFileChange" />
        <button @click="uploadFile">Upload</button>
    </div>
    <div>
        <ul>
            <li v-for="(homeOwner, index) in homeOwners" :key="index">{{ homeOwner }}</li>
        </ul>
        {{ error }}
    </div>
</template>

<script>
export default {
    data() {
        return {
            file: null,
            homeOwners: [],
            error: '',
        };
    },
    methods: {
        onFileChange(event) {
            this.file = event.target.files[0];
        },
        uploadFile() {
            this.error = '';
            const formData = new FormData();
            formData.append('file', this.file);
            axios.post('/csv-upload', formData)
                .then(response => {
                    console.log(JSON.parse(response.data));
                    this.homeOwners = JSON.parse(response.data);
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.error = error.response.data.errors.file[0];
                    }
                });
        },
    },
};
</script>
