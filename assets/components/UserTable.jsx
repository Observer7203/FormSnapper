import React, { useEffect, useState } from "react";
import axios from "axios";

const UserTable = () => {
  const [users, setUsers] = useState([]);
  const [selectedUsers, setSelectedUsers] = useState([]);

  useEffect(() => {
    axios.get("/api/users").then((response) => {
      setUsers(response.data);
    });
  }, []);

  const toggleSelectAll = (event) => {
    if (event.target.checked) {
      setSelectedUsers(users.map((user) => user.id));
    } else {
      setSelectedUsers([]);
    }
  };

  const toggleUserSelection = (id) => {
    setSelectedUsers((prev) =>
      prev.includes(id) ? prev.filter((userId) => userId !== id) : [...prev, id]
    );
  };

  const handleBlockUsers = async () => {
    for (const id of selectedUsers) {
      try {
        const response = await axios.patch(`/api/users/${id}/block`);
        if (response.data.logout) {
          window.location.href = response.data.redirect; // Редиректим на логин
          return; // Прерываем выполнение, чтобы не было нескольких редиректов
        }
        setUsers((prev) =>
          prev.map((user) =>
            user.id === id ? { ...user, status: "blocked" } : user
          )
        );
      } catch (error) {
        console.error("Ошибка блокировки:", error);
      }
    }
  };

  const handleUnblockUsers = async () => {
    for (const id of selectedUsers) {
      try {
        await axios.patch(`/api/users/${id}/unblock`);
        setUsers((prev) =>
          prev.map((user) =>
            user.id === id ? { ...user, status: "active" } : user
          )
        );
      } catch (error) {
        console.error("Ошибка разблокировки:", error);
      }
    }
  };

  const handleDeleteUsers = async () => {
    for (const id of selectedUsers) {
      try {
        await axios.delete(`/api/users/${id}`);
        setUsers((prev) => prev.filter((user) => user.id !== id));
      } catch (error) {
        console.error("Ошибка удаления:", error);
      }
    }
  };

  const styles = {
    container: {
      padding: "1.25rem",
      marginTop: "3rem",
      background: "#fff",
      borderRadius: "0.5rem",
      boxShadow: "0 4px 12px rgba(0, 0, 0, 0.1)",
    },
    table: {
      fontFamily: "var(--bs-body-font-family)",
      boxShadow: "0 .75rem 1.5rem rgba(18, 38, 63, .03)",
      borderRadius: "0.25rem",
    },
    button: {
      margin: "0 10px",
      height: "35px !important",
    },
  };

  return (
    <div className="container" style={styles.container}>
      <div className="toolbar mb-3">
        <button
          className="btn btn-danger waves-effect waves-light"
          style={styles.button}
          onClick={handleBlockUsers}
        >
          <i className="bx bx-lock label-icon"></i> Заблокировать
        </button>
        <button
          className="btn btn-success waves-effect waves-light"
          style={styles.button}
          onClick={handleUnblockUsers}
        >
          <i className="bx bx-lock-open label-icon"></i> Разблокировать
        </button>
        <button
          className="btn btn-warning waves-effect waves-light"
          style={styles.button}
          onClick={handleDeleteUsers}
        >
          <i className="bx bx-trash label-icon"></i> Удалить
        </button>
      </div>
      <table
        className="table align-middle table-nowrap table-hover dt-responsive nowrap w-100"
        style={styles.table}
      >
        <thead className="table-light">
          <tr>
            <th>
              <input
                type="checkbox"
                onChange={toggleSelectAll}
                checked={selectedUsers.length === users.length}
              />
            </th>
            <th>Email</th>
            <th>Status</th>
            <th>Last Login</th>
          </tr>
        </thead>
        <tbody>
          {users.map((user) => (
            <tr key={user.id}>
              <td>
                <input
                  type="checkbox"
                  checked={selectedUsers.includes(user.id)}
                  onChange={() => toggleUserSelection(user.id)}
                />
              </td>
              <td>{user.email}</td>
              <td>
                <span
                  className={`badge ${
                    user.status === "active"
                      ? "bg-success"
                      : "bg-danger"
                  }`}
                >
                  {user.status}
                </span>
              </td>
              <td>{user.lastLogin || "N/A"}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};

export default UserTable;
