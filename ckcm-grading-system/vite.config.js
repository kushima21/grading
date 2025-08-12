import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/login.css',
                'resources/css/default.css',
                'resources/css/course.css',
                'resources/css/my_class.css',
                'resources/css/classes_view.css',
                'resources/css/classes.css',
                'resources/css/allgrades.css',
                'resources/css/studentList.css',
                'resources/css/my_class_archive.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
