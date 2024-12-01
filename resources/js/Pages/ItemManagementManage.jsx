import axios from "axios";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";

import { useForm } from "@inertiajs/react";
import { useEffect, useState } from "react";
import { Head, Link, usePage } from "@inertiajs/react";

export default function ItemManagementManage() {
  const [item, setItem] = useState(null);
  const itemId = usePage().url.match(/\/item-management\/(\d+)\/manage/)[1];
  const { data, setData, put, processing, errors } = useForm({
    name: "",
    price: "",
    initial_stock: "",
    current_stock: "",
    category: "Other",
    image_url: null,
  });

  useEffect(() => {
    axios.get(`/api/items/${itemId}`, {
      headers: {
        "Authorization": "Bearer " + localStorage.getItem("access_token"),
      },
    }).then((response) => {
      const fetchedItem = response.data;
      setItem(fetchedItem);
      setData({
        name: fetchedItem.name,
        price: fetchedItem.price.toString(),
        initial_stock: fetchedItem.initial_stock.toString(),
        current_stock: fetchedItem.current_stock.toString(),
        category: fetchedItem.category,
        image_url: fetchedItem.image_url,
      });
    });
  }, []);

  const handleSubmit = (e) => {
    e.preventDefault();
    // put(`/item-management/${itemId}`, {
    //   preserveScroll: true,
    //   onSuccess: () => {
    //     //
    //   },
    // });
  };

  const handleImageUpload = (e) => {
    const file = e.target.files[0];
    if (file) setData("image_url", file);
  };

  return (
    <AuthenticatedLayout
      header={
        <h2 className="text-xl font-semibold leading-tight text-gray-800">
          Manage Item
        </h2>
      }
    >
      <Head title="Manage Item" />
      <div className="py-12">
        <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white shadow-sm sm:rounded-lg p-6">
            <div className="w-full max-w-sm mx-auto">
              {item && (
                <div className="bg-white border border-gray-200 rounded-lg shadow-md">
                  <div className="relative">
                    <img
                      className="p-8 rounded-t-lg w-full h-72 object-cover"
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
                  </div>
                </div>
              )}
            </div>

            <div>
              <form onSubmit={handleSubmit} className="space-y-4">
                <div>
                  <label
                    htmlFor="name"
                    className="block text-sm font-medium text-gray-700"
                  >
                    Item Name
                  </label>
                  <input
                    type="text"
                    id="name"
                    value={data.name}
                    onChange={(e) => setData("name", e.target.value)}
                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    required
                  />
                  {errors.name && (
                    <p className="text-red-500 text-xs mt-1">{errors.name}</p>
                  )}
                </div>

                <div>
                  <label
                    htmlFor="price"
                    className="block text-sm font-medium text-gray-700"
                  >
                    Price
                  </label>
                  <input
                    type="number"
                    id="price"
                    value={data.price}
                    onChange={(e) => setData("price", e.target.value)}
                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    required
                    min="0"
                    step="0.01"
                  />
                  {errors.price && (
                    <p className="text-red-500 text-xs mt-1">{errors.price}</p>
                  )}
                </div>

                <div>
                  <label
                    htmlFor="category"
                    className="block text-sm font-medium text-gray-700"
                  >
                    Category
                  </label>
                  <select
                    id="category"
                    value={data.category}
                    onChange={(e) => setData("category", e.target.value)}
                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    required
                  >
                    <option value="Food">Food</option>
                    <option value="Drink">Drink</option>
                    <option value="Other">Other</option>
                  </select>
                  {errors.category && (
                    <p className="text-red-500 text-xs mt-1">
                      {errors.category}
                    </p>
                  )}
                </div>

                <div>
                  <label
                    htmlFor="initial_stock"
                    className="block text-sm font-medium text-gray-700"
                  >
                    Initial Stock
                  </label>
                  <input
                    type="number"
                    id="initial_stock"
                    value={data.initial_stock}
                    onChange={(e) => setData("initial_stock", e.target.value)}
                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    required
                    min="0"
                  />
                  {errors.initial_stock && (
                    <p className="text-red-500 text-xs mt-1">
                      {errors.initial_stock}
                    </p>
                  )}
                </div>

                <div>
                  <label
                    htmlFor="current_stock"
                    className="block text-sm font-medium text-gray-700"
                  >
                    Current Stock
                  </label>
                  <input
                    type="number"
                    id="current_stock"
                    value={data.current_stock}
                    onChange={(e) => setData("current_stock", e.target.value)}
                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    required
                    min="0"
                  />
                  {errors.current_stock && (
                    <p className="text-red-500 text-xs mt-1">
                      {errors.current_stock}
                    </p>
                  )}
                </div>

                <div>
                  <label
                    htmlFor="image"
                    className="block text-sm font-medium text-gray-700"
                  >
                    Item Image
                  </label>
                  <input
                    type="file"
                    id="image"
                    onChange={handleImageUpload}
                    accept="image/*"
                    className="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                  />
                  {errors.image_url && (
                    <p className="text-red-500 text-xs mt-1">
                      {errors.image_url}
                    </p>
                  )}
                </div>

                <div className="flex justify-end space-x-4">
                  <Link
                    href="/item-management"
                    className="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50"
                  >
                    Cancel
                  </Link>
                  <button
                    type="submit"
                    disabled={processing}
                    className="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                  >
                    {processing ? "Updating..." : "Update Item"}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
