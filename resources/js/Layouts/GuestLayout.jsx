import ApplicationLogo from "@/Components/ApplicationLogo";

import { Link } from "@inertiajs/react";

export default function GuestLayout({ children, laravelVersion, phpVersion }) {
    return (
        <div className="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0">
            <div>
                <Link href="/">
                    <ApplicationLogo className="h-20 w-20 fill-current text-gray-500" />
                </Link>
            </div>
            <div className="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg">
                {children}
            </div>
            <div className="mt-4 flex text-sm text-gray-600 underline hover:no-underline">
                <a href="/api/documentation" target="_blank">
                    API Documentation (Swagger)
                </a>
            </div>
            <div className="flex mt-2 text-sm text-gray-600">
                <span>Laravel v{laravelVersion} (PHP v{phpVersion})</span>
                <span>&nbsp;&copy; Rivane Rasetiansyah</span>
            </div>
        </div>
    );
}
