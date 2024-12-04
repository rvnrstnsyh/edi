import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";
import { useEffect, useState } from "react";
import axios from "axios";

export default function Reports() {
  const [stocks, setStocks] = useState([]);
  const [transactions, setTransactions] = useState([]);

  useEffect(() => {
    axios
      .get("/api/reports/stock", {
        headers: {
          Authorization: "Bearer " + localStorage.getItem("access_token"),
        },
      })
      .then((response) => setStocks(response.data));

    axios
      .get("/api/reports/transaction", {
        headers: {
          Authorization: "Bearer " + localStorage.getItem("access_token"),
        },
      })
      .then((response) => setTransactions(response.data));
  }, []);

  return (
    <AuthenticatedLayout
      header={
        <h2 className="text-xl font-semibold leading-tight text-gray-800">
          Reports
        </h2>
      }
    >
      <Head title="Reports" />
      <div className="py-12">
        <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
          {/* Tabel Stok */}
          <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg mb-8">
            <div className="p-6 text-gray-900">
              <div className="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table className="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                  <caption class="p-5 text-lg font-semibold text-left rtl:text-right text-gray-900 bg-white dark:text-white dark:bg-gray-800">
                    Our products
                    <p class="mt-1 text-sm font-normal text-gray-500 dark:text-gray-400">
                      Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                      Soluta tenetur id neque placeat aperiam accusamus ipsam
                      eligendi libero. Expedita qui omnis natus beatae ducimus
                      non eaque? Sit sunt quasi aspernatur. Beatae ad in ex
                      molestiae, quis odit reprehenderit tempora optio ipsa fuga
                      quisquam quod odio quae autem earum alias quia!
                    </p>
                  </caption>
                  <thead className="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                      <th scope="col" className="px-6 py-3">Product name</th>
                      <th scope="col" className="px-6 py-3">Category</th>
                      <th scope="col" className="px-6 py-3">Stock</th>
                      <th scope="col" className="px-6 py-3">Price</th>
                    </tr>
                  </thead>
                  <tbody>
                    {stocks.map((stock, index) => (
                      <tr
                        key={index}
                        className="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
                      >
                        <th
                          scope="row"
                          className="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                        >
                          {stock.name}
                        </th>
                        <td className="px-6 py-4">{stock.category}</td>
                        <td className="px-6 py-4">{stock.current_stock}</td>
                        <td className="px-6 py-4">
                          Rp {parseInt(stock.price).toLocaleString("id-ID")}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          {/* Tabel Histori Transaksi */}
          <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div className="p-6 text-gray-900">
              <div className="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table className="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                  <caption class="p-5 text-lg font-semibold text-left rtl:text-right text-gray-900 bg-white dark:text-white dark:bg-gray-800">
                    Transaction Histories
                    <p class="mt-1 text-sm font-normal text-gray-500 dark:text-gray-400">
                      Lorem ipsum, dolor sit amet consectetur adipisicing elit.
                      Incidunt cupiditate saepe vitae earum, culpa excepturi
                      minus pariatur quaerat, eum eligendi dolor cumque,
                      voluptatum aliquid adipisci distinctio. Alias, neque saepe
                      eligendi officia ratione quibusdam nihil quos soluta
                      accusantium asperiores, deleniti, magni excepturi
                      voluptate amet laborum aut? Minima eaque aspernatur cumque
                      reprehenderit!
                    </p>
                  </caption>
                  <thead className="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                      <th scope="col" className="px-6 py-3">Transaction ID</th>
                      <th scope="col" className="px-6 py-3">Product Name</th>
                      <th scope="col" className="px-6 py-3">Category</th>
                      <th scope="col" className="px-6 py-3">Quantity</th>
                      <th scope="col" className="px-6 py-3">Total Price</th>
                      <th scope="col" className="px-6 py-3">Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    {transactions.map((transaction, index) => (
                      <tr
                        key={index}
                        className="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
                      >
                        <td className="px-6 py-4">{transaction.id}</td>
                        <td className="px-6 py-4">{transaction.item.name}</td>
                        <td className="px-6 py-4">
                          {transaction.item.category}
                        </td>
                        <td className="px-6 py-4">{transaction.quantity}</td>
                        <td className="px-6 py-4">
                          Rp {parseInt(transaction.total_price).toLocaleString(
                            "id-ID",
                          )}
                        </td>
                        <td className="px-6 py-4">
                          {new Date(transaction.transaction_date)
                            .toLocaleString("id-ID")}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
