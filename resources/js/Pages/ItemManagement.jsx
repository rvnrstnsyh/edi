import axios from "axios";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";

import { Head, Link } from "@inertiajs/react";
import { useEffect, useState } from "react";

export default function ItemManagement() {
  const [items, setItems] = useState([]);

  useEffect(() => {
    axios.get("/api/items", {
      headers: {
        "Authorization": "Bearer " + localStorage.getItem("access_token"),
      },
    }).then((response) => setItems(response.data));
  }, []);

  return (
    <AuthenticatedLayout
      header={
        <h2 className="text-xl font-semibold leading-tight text-gray-800">
          Item Management
        </h2>
      }
    >
      <Head title="Item Management" />
      <div className="py-12">
        <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
          <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div className="p-6 text-gray-900">
              <div className="mt-4 mb-5 flex justify-end items-end space-x-2">
                <button className="max-w-sm text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition duration-300 ease-in-out">
                  Add New Item
                </button>
              </div>
              <hr />
              <br />
              <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                {items.map((item) => (
                  <div
                    key={item.id}
                    className="w-full max-w-sm mx-auto bg-white border border-gray-200 rounded-lg shadow-md"
                  >
                    <div className="relative">
                      <img
                        className="p-8 rounded-t-lg w-full h-52 object-cover"
                        src={item.image_url || "/placeholder-image.png"}
                        alt={item.name}
                        onError={(e) => {
                          e.target.src = "/placeholder-image.png";
                          e.target.onerror = null;
                        }}
                      />
                      {item.current_stock <= 10 && (
                        <span className="absolute top-4 right-4 bg-red-500 text-white text-xs font-bold px-2.5 py-0.5 rounded">
                          Low Stock
                        </span>
                      )}
                    </div>
                    <div className="px-5 pb-5">
                      <h5 className="text-lg font-semibold tracking-tight text-gray-900 h-16 overflow-hidden">
                        {item.name}
                      </h5>
                      <hr className="border-t-2 border-gray-200" />
                      <div className="flex items-center justify-between mt-4">
                        <span className="text-2xl font-bold text-gray-900">
                          <span className="text-sm">Rp.</span>
                          {item.price.toLocaleString("id-ID", {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                          })}
                        </span>
                        <div className="flex items-center space-x-2">
                          <span className="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                            {item.current_stock}
                          </span>
                        </div>
                      </div>
                      <div className="mt-4 flex space-x-2">
                        <Link href={`item-management/${item.id}/manage`}>
                          <button className="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition duration-300 ease-in-out">
                            Manage
                          </button>
                        </Link>
                        <Link href={`item-management/${item.id}/delete`}>
                          <button className="w-full text-red-700 border border-red-700 hover:bg-red-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition duration-300 ease-in-out">
                            Delete
                          </button>
                        </Link>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
              {items.length === 0 && (
                <div className="text-center py-10 text-gray-500">
                  No items found
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
