import axios from "axios";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";

import { Head } from "@inertiajs/react";
import { useEffect, useState } from "react";

export default function Dashboard() {
    const [items, setItems] = useState([]);

    useEffect(() => {
        axios.get("/api/item", {
            headers: {
                "Authorization": "Bearer" +
                    localStorage.getItem("access_token"),
            },
        }).then((response) => setItems(response.data));
    }, []);

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Dashboard
                </h2>
            }
        >
            <Head title="Dashboard" />
            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            You're logged in! {items}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
