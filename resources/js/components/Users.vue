<template>
    <div class="container">
    <div v-if="$gate.isAdminAuthor()" class="row mt-5">
    <div class="col-12">
    <div class="card">
    <div class="card-header">
    <h3 class="card-title">Users List</h3>

    <div class="card-tools">
        <button class="btn btn-success" @click="newUser">
            Add New <i class="fas fa-user-plus fa-fw"></i>
        </button>
    </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body table-responsive p-0">
        <table class="table table-hover">
        <tbody><tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Type</th>
        <th>Create Date</th>
        <th>Modify</th>
        </tr>

        <tr v-for="user in users.data" :key="user.id">
        <td>{{user.id}}</td>
        <td>{{user.name}}</td>
        <td>{{user.email}}</td>
        <td>{{user.type | uptext}}</td>
        <td>{{user.created_at | myDate}}</td>
        <td>
            <a @click="editModal(user)" href="#">
                <i class="fas fa-edit blue"></i>
            </a>
            /
            <a @click="deleteUser(user.id)" href="#">
                <i class="fas fa-trash red"></i>
            </a>
        </td>
    </tr>

    </tbody></table>
    </div>
    <!-- /.card-body -->
        <div class="card-footer">
            <pagination :data="users" @pagination-change-page="getResults"></pagination>
        </div>
    </div>
    <!-- /.card -->
    </div>
    </div>

    <div v-if="!$gate.isAdminAuthor()">
        <not-found></not-found>
    </div>

        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1"
             role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
            <div class="modal-header">
            <h5 v-show="!editmode" class="modal-title" id="exampleModalLabel">Add New</h5>
            <h5 v-show="editmode" class="modal-title" id="exampleModalLabel">Edit</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div>
        <form @submit.prevent="editmode ? updateUser() : createUser()">
        <div class="modal-body">
            <div class="form-group">
                <input v-model="form.name" type="text" name="name" id="name"
                        placeholder="Enter Name"
                       class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                <has-error :form="form" field="name"></has-error>
            </div>

            <div class="form-group">
                <input v-model="form.email" type="text" name="email" id="email"
                        placeholder="Enter Email"
                       class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                <has-error :form="form" field="email"></has-error>
            </div>

            <div class="form-group">
                <textarea v-model="form.bio" type="text" name="bio" id="bio"
                           placeholder="Short Bio for user (optional)"
                       class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }">
                </textarea>
                <has-error :form="form" field="bio"></has-error>
            </div>

            <div class="form-group">
                <select v-model="form.type" type="text" name="type"  id="type"
                          class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                    <option value="">Select User Role</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                    <option value="author">Author</option>
                </select>
                <has-error :form="form" field="type"></has-error>
            </div>
            <div class="form-group">
                <input v-model="form.password" type="password" name="password" id="password"
                       placeholder="Enter Password"
                       class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                <has-error :form="form" field="password"></has-error>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>
            <button v-show="editmode" type="submit" class="btn btn-primary">Update</button>
        </div>
        </form>
            </div>
        </div>
        </div>
    </div>
</template>

<script>
    export default {
        data () {
            return{
                editmode:false,
                users:{},
                // Create a new form instance
                form: new Form({
                    id:'',
                    name: '',
                    email: '',
                    bio: '',
                    type: '',
                    password: ''
                })
            }
        },
        methods:{
            getResults(page = 1) {
                axios.get('api/user?page=' + page+'&api_token='+api_token)
                    .then(response => {
                        this.users = response.data;
                    });
            },
            updateUser(){
                this.$Progress.start();
                this.form.put('api/user/'+this.form.id+'?api_token='+api_token)
                    .then(({data})=>{
                        console.log(data);
                        $('#addNew').modal('hide');
                        Swal.fire(
                            'update',
                            'Information has been updated.',
                            'success'
                        );
                        this.$Progress.finish();
                        Fire.$emit('userReload');
                    }).catch(()=>{
                        this.$Progress.fail();
                    });
            },
            editModal(user){
                this.editmode=true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(user);
            },
            newUser(){
                this.editmode=false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            deleteUser(id){
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        this.form.delete("api/user/"+id).then(()=>{
                            Swal.fire(
                                'Deleted!',
                                'Your file has been deleted.',
                                'success'
                            );
                            Fire.$emit('userReload');
                        }).catch(()=>{
                            Swal("failed!","There was somthing wrong.","warning");
                        });

                    }
                })
            },
            loadUsers(){
              if(this.$gate.isAdminAuthor()){
                  axios.get("api/user?api_token="+api_token).then(({data})=>{this.users=data})
              }
            },
            createUser(){
                this.$Progress.start();
                this.form.post('/api/user?api_token='+api_token)
                    .then(({data})=>{
                        Fire.$emit('userReload');
                        console.log(data);
                        $('#addNew').modal('hide');
                        this.$Progress.finish();
                        Toast.fire({
                            type: 'success',
                            title: 'User Created in successfully'
                        });
                    });
            }
        },
        created(){
            Fire.$on('searching',()=>{
               let query=this.$parent.search;
               axios.get('api/findUser?q='+query+'&api_token='+api_token)
                .then((data)=>{
                   console.log(data);
                   this.users=data.data
                }).catch((err)=>{
                   console.log(err);
                });
            });
            this.loadUsers();
            self=this;
            Fire.$on('userReload',()=>{
                self.loadUsers();
            });
//            setInterval(function(){ self.loadUsers(); }, 3000);
        },
        mounted() {
            // Fetch initial results
            this.getResults();
        }
    }
</script>
