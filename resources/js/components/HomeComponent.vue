<template>
    <div>
        <input type="file" @change="onFileChange" />
        <button @click="uploadFile">Upload</button>
    </div>
    <div>
        <br>
        <button @click="changeList" v-if="homeOwners.length">{{buttonText}}</button>
        <div v-if="showRaw">{{homeOwners}}</div>
        <ul v-else>
            <li v-for="(homeOwner, index) in homeOwners" :key="index">
                Home number {{ index + 1 }}
                <ul>
                    <li v-for="(owner,index) in homeOwner" :key="index">
                        Title: {{ owner.title }}<br>
                        First Name: {{ owner.first_name ?? 'null'}}<br>
                        Initial: {{ owner.initial ?? 'null'}}<br>
                        Last Name: {{ owner.last_name }}
                    </li>
                </ul>
            </li>
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
            showRaw: false,
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
                    this.homeOwners = JSON.parse(response.data);
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.error = error.response.data.errors.file[0];
                    }
                });
        },

        changeList() {
            this.showRaw = !this.showRaw;
        }
    },
    computed: {
        buttonText() {
            return this.showRaw ? 'Show List' : 'Show Raw';
        },
    },
};
</script>
