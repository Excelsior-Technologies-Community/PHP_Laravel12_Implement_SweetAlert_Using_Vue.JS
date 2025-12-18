# PHP_Laravel12_Implement_SweetAlert_Using_Vue.JS

---

##  Overview

Stack used:
- Laravel 12
- Breeze Authentication
- Inertia.js
- Vue 3
- SweetAlert2

---

##  Features

- Full CRUD (Create, Read, Update, Delete)
- SweetAlert2 success popup
- SweetAlert2 delete confirmation
- Inertia SPA (no page reload)
- Laravel 12 compatible
- Clean & beginner-friendly

---

##  Complete Folder Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── PostController.php
│   └── Middleware/
│       └── HandleInertiaRequests.php
├── Models/
│   └── Post.php

database/
└── migrations/
    └── 2025_01_01_000000_create_posts_table.php

resources/
├── js/
│   ├── app.js
│   ├── bootstrap.js
│   └── Pages/
│       └── Posts/
│           ├── Index.vue
│           ├── Create.vue
│           └── Edit.vue
└── views/
    └── app.blade.php

routes/
└── web.php

.env
README.md
```

---

##  STEP 1: Install Laravel 12

```bash
composer create-project laravel/laravel laravel12-sweetalert
```

---

##  STEP 2: Install Breeze + Inertia + Vue

```bash
composer require laravel/breeze --dev

php artisan breeze:install vue

npm install

php artisan migrate

npm run dev

php artisan serve
```

---

## 🟦 STEP 3: Database Configuration (.env)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=alert
DB_USERNAME=root
DB_PASSWORD=
```

---

##  STEP 4: Create Migration

```bash
php artisan make:model Post -m
```

### database/migrations/xxxx_create_posts_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
```

```bash
php artisan migrate
```

---

##  STEP 5: Model (FULL)

### app/Models/Post.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title','description'];
}
```

---

##  STEP 6: Controller (FULL)

### app/Http/Controllers/PostController.php

```php
<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index()
    {
        return Inertia::render('Posts/Index', [
            'posts' => Post::latest()->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('Posts/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        Post::create($request->all());

        return redirect()->route('posts.index')
            ->with('success','Post Created Successfully');
    }

    public function edit(Post $post)
    {
        return Inertia::render('Posts/Edit', ['post'=>$post]);
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        $post->update($request->all());

        return redirect()->route('posts.index')
            ->with('success','Post Updated Successfully');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return back()->with('success','Post Deleted Successfully');
    }
}
```

---

##  STEP 7: Routes (FULL)

### routes/web.php

```php
<?php

use App\Http\Controllers\PostController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

});
```

---

##  STEP 8: Inertia Flash Share

### app/Http/Middleware/HandleInertiaRequests.php

```php
   'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
            ],
        ];
```

---

##  STEP 9: app.js (FULL)

### resources/js/app.js

```js
import '../css/app.css'
import './bootstrap'
import Swal from 'sweetalert2'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createApp, h } from 'vue'

createInertiaApp({
  resolve: name =>
    resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    const vueApp = createApp({ render: () => h(App, props) })

    vueApp.mixin({
      mounted() {
        if (this.$page.props.flash?.success) {
          Swal.fire({
            icon: 'success',
            title: 'Success',
            text: this.$page.props.flash.success,
            timer: 2000,
            showConfirmButton: false,
          })
        }
      }
    })

    vueApp.use(plugin).mount(el)
  },
})
```

---

##  STEP 10: app.blade.php (FULL)

### resources/views/app.blade.php

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel SweetAlert CRUD</title>
    @vite(['resources/js/app.js'])
</head>
<body>
    @inertia
</body>
</html>
```

---

##  STEP 11: Vue Pages 

### resources/js/Pages/Posts/Index.vue

```vue
<script setup>
import { Link, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
defineProps({ posts:Array })

const destroyPost = (id) => {
  Swal.fire({
    title: 'Are you sure?',
    text: 'This record will be deleted!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete it'
  }).then(result => {
    if (result.isConfirmed) {
      router.delete(`/posts/${id}`)
    }
  })
}
</script>

<template>
  <div>
    <h1>Posts</h1>
    <Link href="/posts/create">Add Post</Link>

    <table>
      <tr v-for="post in posts" :key="post.id">
        <td>{{ post.title }}</td>
        <td>
          <Link :href="`/posts/${post.id}/edit`">Edit</Link>
          <button @click="destroyPost(post.id)">Delete</button>
        </td>
      </tr>
    </table>
  </div>
</template>
```

---

### resources/js/Pages/Posts/Create.vue

```vue
<script setup>
import { useForm, Link } from '@inertiajs/vue3'

const form = useForm({
  title: '',
  description: ''
})

const submit = () => {
  form.post('/posts')
}
</script>

<template>
  <form @submit.prevent="submit">
    <input v-model="form.title" placeholder="Title" />
    <textarea v-model="form.description"></textarea>
    <button>Save</button>
    <Link href="/posts">Back</Link>
  </form>
</template>
```

---

### resources/js/Pages/Posts/Edit.vue

```vue
<script setup>
import { useForm, Link } from '@inertiajs/vue3'
const props = defineProps({ post:Object })

const form = useForm({
  title: props.post.title,
  description: props.post.description
})

const update = () => {
  form.put(`/posts/${props.post.id}`)
}
</script>

<template>
  <form @submit.prevent="update">
    <input v-model="form.title" />
    <textarea v-model="form.description"></textarea>
    <button>Update</button>
    <Link href="/posts">Back</Link>
  </form>
</template>
```

---

##  Final URL

```
http://127.0.0.1:8000/posts
```
CREATE ( SWEET ALERT ):-

<img width="1919" height="661" alt="Screenshot 2025-12-18 152111" src="https://github.com/user-attachments/assets/bae37a61-e66f-44af-ad46-0bb8e8009345" />

UPDATE ( SWEET ALERT ):-

<img width="1919" height="683" alt="Screenshot 2025-12-18 152331" src="https://github.com/user-attachments/assets/a87288be-9027-44f8-998e-7236ac6bfcdd" />

DELETE ( SWEET ALERT ):- 

<img width="1919" height="708" alt="Screenshot 2025-12-18 152124" src="https://github.com/user-attachments/assets/8afc8394-4951-463e-a4d5-dacec486c6a9" />


---
