import axios from "axios";
import Checkbox from "@/Components/Checkbox";
import TextInput from "@/Components/TextInput";
import GuestLayout from "@/Layouts/GuestLayout";
import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import PrimaryButton from "@/Components/PrimaryButton";

import { useState } from "react";
import { Head, Link, usePage } from "@inertiajs/react";

export default function Login({
    status,
    canResetPassword,
    laravelVersion,
    phpVersion,
}) {
    const props = usePage().props;
    const [processing, setProcessing] = useState(false);
    const [formData, setFormData] = useState({
        _token: props.csrf_token,
        email: "",
        password: "",
        remember: false,
    });
    const [errors, setErrors] = useState({});
    const validate = () => {
        const newErrors = {};
        // Email validation
        if (!formData.email) {
            newErrors.email = "Email is required";
        } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
            newErrors.email = "Email is invalid";
        } else if (formData.email.length > 255) {
            newErrors.email = "Email must be less than 255 characters";
        }
        // Password validation
        if (!formData.password) {
            newErrors.password = "Password is required";
        } else if (formData.password.length < 8) {
            newErrors.password = "Password must be at least 8 characters";
        }
        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    };

    const handleSubmit = async (event) => {
        event.preventDefault();
        setProcessing(true);
        // Validate form before submission
        if (!validate()) return setProcessing(false);
        try {
            const response = await axios.post("/", formData, {
                headers: {
                    "Accept": "application/json",
                },
            });
            localStorage.setItem("access_token", response.data.access_token);
            window.location.replace("/dashboard");
        } catch (error) {
            setProcessing(false);
            if (error.response) {
                console.error("Login Error:", error.response.data);
                setErrors((prevErrors) => ({
                    ...prevErrors,
                    server: error.response.data.message || "Login failed",
                }));
            } else {
                console.error("Network Error:", error.message);
            }
        }
    };

    const handleChange = (event) => {
        const { id, value, type, checked } = event.target;
        setFormData((prevState) => ({
            ...prevState,
            [id]: type === "checkbox" ? checked : value,
        }));
    };

    return (
        <GuestLayout laravelVersion={laravelVersion} phpVersion={phpVersion}>
            <Head title="Log in" />

            {status && (
                <div className="mb-4 text-sm font-medium text-green-600">
                    {status}
                </div>
            )}

            <form onSubmit={handleSubmit}>
                <div>
                    <InputLabel htmlFor="email" value="Email" />
                    <TextInput
                        id="email"
                        type="email"
                        name="email"
                        value={formData.email}
                        className="mt-1 block w-full"
                        autoComplete="username"
                        isFocused={true}
                        onChange={handleChange}
                        placeholder="chernobyl@example.com"
                    />
                    <InputError message={errors.email} className="mt-2" />
                    <InputError message={errors.server} className="mt-2" />
                </div>

                <div className="mt-4">
                    <InputLabel htmlFor="password" value="Password" />
                    <TextInput
                        id="password"
                        type="password"
                        name="password"
                        value={formData.password}
                        className="mt-1 block w-full"
                        autoComplete="current-password"
                        onChange={handleChange}
                        placeholder="********"
                    />
                    <InputError message={errors.password} className="mt-2" />
                </div>

                <div className="block">
                    <label className="flex items-center">
                        <Checkbox
                            name="remember"
                            checked={formData.remember}
                            onChange={(event) => {
                                setFormData((prevState) => ({
                                    ...prevState,
                                    remember: event.target.checked,
                                }));
                            }}
                        />
                        <span className="ms-2 mt-4 text-sm text-gray-600 leading-3">
                            Keep me signed in
                            <br />
                            <span className="text-xs text-gray-400">
                                Recommended on trusted devices.&nbsp;
                                <a
                                    className="underline hover:no-underline"
                                    href="#"
                                >
                                    Why?
                                </a>
                            </span>
                        </span>
                    </label>
                </div>

                <div className="mt-4 flex items-center justify-end">
                    {canResetPassword && (
                        <>
                            <Link
                                href={route("password.request")}
                                className="mr-4 rounded-md text-sm text-gray-600 underline hover:no-underline"
                            >
                                Forgot password?
                            </Link>
                            <Link
                                href={route("register")}
                                className="rounded-md text-sm text-gray-600 underline hover:no-underline"
                            >
                                Don't have an account?
                            </Link>
                        </>
                    )}
                    <PrimaryButton className="ms-4" disabled={processing}>
                        Log in
                    </PrimaryButton>
                </div>
            </form>
        </GuestLayout>
    );
}
