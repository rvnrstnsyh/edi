import axios from "axios";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";

import { useState } from "react";
import { Head, Link } from "@inertiajs/react";

export default function ItemManagementManage() {
  const [formData, setFormData] = useState({
    name: "",
    price: "",
    category: "Other",
    initial_stock: "",
    image_url: null,
  });
  const [errors, setErrors] = useState({});
  const [processing, setProcessing] = useState(false);
  const [imageFile, setImageFile] = useState(null);

  const handleInputChange = (e) => {
    const { id, value } = e.target;
    setFormData((prevState) => ({
      ...prevState,
      [id]: value,
    }));
  };

  const handleImageUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
      setImageFile(file);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setProcessing(true);
    setErrors({});

    const payload = { ...formData };

    try {
      const token = localStorage.getItem("access_token");
      if (imageFile) {
        const imageFormData = new FormData();
        imageFormData.append("image", imageFile);
        await axios.post(
          "/api/upload-image",
          imageFormData,
          {
            headers: {
              "Content-Type": "multipart/form-data",
              "Authorization": `Bearer ${token}`,
            },
          },
        ).then(async (response) => {
          payload.image_url = response.data.image_url;
          await axios.post("/api/items", payload, {
            headers: {
              "Authorization": `Bearer ${token}`,
              "Content-Type": "application/json",
            },
          });
          window.location.href = "/item-management";
        });
      }
    } catch (error) {
      console.error("Error updating item:", error);
      if (error.response && error.response.data.errors) {
        setErrors(error.response.data.errors);
      } else {
        alert("An error occurred while updating the item");
      }
      setProcessing(false);
    }
  };

  return (
    <AuthenticatedLayout
      header={
        <h2 className="text-xl font-semibold leading-tight text-gray-800">
          Create New Item
        </h2>
      }
    >
      <Head title="Manage Item" />
      <div className="py-12">
        <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white shadow-sm sm:rounded-lg p-6">
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
                    value={formData.name}
                    onChange={handleInputChange}
                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    required
                  />
                  {errors.name && (
                    <p className="text-red-500 text-xs mt-1">
                      {errors.name[0]}
                    </p>
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
                    value={formData.price}
                    onChange={handleInputChange}
                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    required
                    min="0"
                    step="0.01"
                  />
                  {errors.price && (
                    <p className="text-red-500 text-xs mt-1">
                      {errors.price[0]}
                    </p>
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
                    value={formData.category}
                    onChange={handleInputChange}
                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    required
                  >
                    <option value="Food">Food</option>
                    <option value="Drink">Drink</option>
                    <option value="Other">Other</option>
                  </select>
                  {errors.category && (
                    <p className="text-red-500 text-xs mt-1">
                      {errors.category[0]}
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
                    value={formData.initial_stock}
                    onChange={handleInputChange}
                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    required
                    min="0"
                  />
                  {errors.initial_stock && (
                    <p className="text-red-500 text-xs mt-1">
                      {errors.initial_stock[0]}
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
                      {errors.image_url[0]}
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
                    {processing ? "Creating..." : "Create Item"}
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
