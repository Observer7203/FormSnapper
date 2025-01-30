import './bootstrap.js';
import 'bootstrap/dist/css/bootstrap.min.css';
import React from "react";
import ReactDOM from "react-dom/client"; // для React 18
import UserTable from "./components/UserTable";
import "boxicons/css/boxicons.min.css";


// Создаём корень React
const root = ReactDOM.createRoot(document.getElementById("root"));
root.render(<UserTable />);

import './styles/app.css';
import '@mdi/font/css/materialdesignicons.min.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');


if (rootElement) {
    // Если элемент существует, рендерим React-компонент
    const root = ReactDOM.createRoot(rootElement);
    root.render(<UserTable />);
}