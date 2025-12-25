<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Link } from '@inertiajs/vue3';
import { Facebook, Instagram, Twitter, Mail, Phone, MapPin } from 'lucide-vue-next';
import { home } from '@/routes';
</script>

<template>
    <footer class="relative w-screen overflow-hidden border-t border-sidebar-border/50 bg-linear-to-br from-slate-50 via-white to-blue-50/30 dark:from-slate-950 dark:via-slate-900 dark:to-blue-950/20" style="margin-left: calc(-50vw + 50%); margin-right: calc(-50vw + 50%);">
        <!-- Decorative background elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -right-20 top-0 size-96 rounded-full bg-blue-200/10 blur-3xl dark:bg-blue-800/5"></div>
            <div class="absolute -left-20 bottom-0 size-96 rounded-full bg-purple-200/10 blur-3xl dark:bg-purple-800/5"></div>
        </div>

        <div class="container relative mx-auto w-full max-w-7xl px-4 py-12 lg:py-16">
            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                <!-- Company Info -->
                <div class="space-y-5">
                    <Link :href="home()" class="inline-block transition-transform duration-200 hover:scale-105">
                        <AppLogo />
                    </Link>
                    <p class="text-sm leading-relaxed text-muted-foreground">
                        Your trusted destination for modern furniture and home decor.
                        We bring you stylish designs at affordable prices with free
                        delivery and assembly services.
                    </p>
                    <div class="flex gap-3">
                        <a
                            href="#"
                            class="group flex size-10 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition-all duration-200 hover:scale-110 hover:bg-blue-500 hover:text-white hover:shadow-lg hover:shadow-blue-500/30 dark:bg-slate-800 dark:text-slate-400 dark:hover:bg-blue-500 dark:hover:text-white"
                            aria-label="Facebook"
                        >
                            <Facebook class="size-5 transition-transform duration-200 group-hover:scale-110" />
                        </a>
                        <a
                            href="#"
                            class="group flex size-10 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition-all duration-200 hover:scale-110 hover:bg-pink-500 hover:text-white hover:shadow-lg hover:shadow-pink-500/30 dark:bg-slate-800 dark:text-slate-400 dark:hover:bg-pink-500 dark:hover:text-white"
                            aria-label="Instagram"
                        >
                            <Instagram class="size-5 transition-transform duration-200 group-hover:scale-110" />
                        </a>
                        <a
                            href="#"
                            class="group flex size-10 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition-all duration-200 hover:scale-110 hover:bg-sky-500 hover:text-white hover:shadow-lg hover:shadow-sky-500/30 dark:bg-slate-800 dark:text-slate-400 dark:hover:bg-sky-500 dark:hover:text-white"
                            aria-label="Twitter"
                        >
                            <Twitter class="size-5 transition-transform duration-200 group-hover:scale-110" />
                        </a>
                    </div>
                </div>

                <!-- Shop -->
                <div>
                    <h3 class="mb-5 text-base font-semibold text-foreground">Shop</h3>
                    <ul class="space-y-3 text-sm">
                        <li>
                            <Link
                                href="#"
                                class="group inline-flex items-center text-muted-foreground transition-all duration-200 hover:translate-x-1 hover:text-primary"
                            >
                                <span class="mr-2 opacity-0 transition-opacity duration-200 group-hover:opacity-100">→</span>
                                New Arrivals
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="#"
                                class="group inline-flex items-center text-muted-foreground transition-all duration-200 hover:translate-x-1 hover:text-primary"
                            >
                                <span class="mr-2 opacity-0 transition-opacity duration-200 group-hover:opacity-100">→</span>
                                Best Sellers
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="#"
                                class="group inline-flex items-center text-muted-foreground transition-all duration-200 hover:translate-x-1 hover:text-primary"
                            >
                                <span class="mr-2 opacity-0 transition-opacity duration-200 group-hover:opacity-100">→</span>
                                Sale
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="#"
                                class="group inline-flex items-center text-muted-foreground transition-all duration-200 hover:translate-x-1 hover:text-primary"
                            >
                                <span class="mr-2 opacity-0 transition-opacity duration-200 group-hover:opacity-100">→</span>
                                Gift Cards
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Customer Service -->
                <div>
                    <h3 class="mb-5 text-base font-semibold text-foreground">Customer Service</h3>
                    <ul class="space-y-3 text-sm">
                        <li>
                            <Link
                                href="#"
                                class="group inline-flex items-center text-muted-foreground transition-all duration-200 hover:translate-x-1 hover:text-primary"
                            >
                                <span class="mr-2 opacity-0 transition-opacity duration-200 group-hover:opacity-100">→</span>
                                Contact Us
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="#"
                                class="group inline-flex items-center text-muted-foreground transition-all duration-200 hover:translate-x-1 hover:text-primary"
                            >
                                <span class="mr-2 opacity-0 transition-opacity duration-200 group-hover:opacity-100">→</span>
                                Shipping Info
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="#"
                                class="group inline-flex items-center text-muted-foreground transition-all duration-200 hover:translate-x-1 hover:text-primary"
                            >
                                <span class="mr-2 opacity-0 transition-opacity duration-200 group-hover:opacity-100">→</span>
                                Returns & Exchanges
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="#"
                                class="group inline-flex items-center text-muted-foreground transition-all duration-200 hover:translate-x-1 hover:text-primary"
                            >
                                <span class="mr-2 opacity-0 transition-opacity duration-200 group-hover:opacity-100">→</span>
                                FAQ
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div>
                    <h3 class="mb-5 text-base font-semibold text-foreground">Newsletter</h3>
                    <p class="mb-5 text-sm leading-relaxed text-muted-foreground">
                        Subscribe to get special offers and updates
                    </p>
                    <form class="space-y-3">
                        <Input
                            type="email"
                            placeholder="Enter your email"
                            class="h-11 w-full border-2 transition-all duration-200 focus:border-primary/50 focus:ring-2 focus:ring-primary/20 hover:border-primary/30"
                        />
                        <Button 
                            type="submit" 
                            class="group/btn h-11 w-full bg-linear-to-r from-blue-600 to-blue-500 text-white shadow-lg shadow-blue-500/20 transition-all duration-200 hover:scale-[1.02] hover:shadow-xl hover:shadow-blue-500/30 cursor-pointer" 
                            size="sm"
                        >
                            Subscribe
                            <Mail class="ml-2 size-4 transition-transform duration-200 group-hover/btn:translate-x-1" />
                        </Button>
                    </form>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div
                class="mt-12 border-t border-sidebar-border/50 pt-8 text-center text-sm text-muted-foreground"
            >
                <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
                    <p class="font-medium">© {{ new Date().getFullYear() }} Alfi's Store. All rights reserved.</p>
                    <div class="flex gap-6">
                        <Link
                            href="#"
                            class="transition-all duration-200 hover:scale-105 hover:text-primary hover:underline"
                        >
                            Privacy Policy
                        </Link>
                        <Link
                            href="#"
                            class="transition-all duration-200 hover:scale-105 hover:text-primary hover:underline"
                        >
                            Terms of Service
                        </Link>
                        <Link
                            href="#"
                            class="transition-all duration-200 hover:scale-105 hover:text-primary hover:underline"
                        >
                            Cookie Policy
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>

