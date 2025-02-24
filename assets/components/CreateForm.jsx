import React, { useState } from "react";
import axios from "axios";

function CreateForm() {
  const [title, setTitle] = useState("");
  const [description, setDescription] = useState("");

  const handleSubmit = async (event) => {
    event.preventDefault();

    try {
      const response = await axios.post("/api/forms", {
        title,
        description,
        questions: []
      });

      if (response.status === 200) {
        alert("Форма успешно создана!");
        window.location.href = "/forms"; // Вместо useNavigate()
      }
    } catch (error) {
      console.error("Ошибка при создании формы:", error);
      alert("Не удалось создать форму.");
    }
  };

  return (
    <div className="container mt-4">
      <h2 className="text-center">Создать новую форму</h2>
      <form onSubmit={handleSubmit}>
        <div className="mb-3">
          <label className="form-label">Название формы</label>
          <input
            type="text"
            className="form-control"
            value={title}
            onChange={(e) => setTitle(e.target.value)}
            required
          />
        </div>
        <div className="mb-3">
          <label className="form-label">Описание</label>
          <textarea
            className="form-control"
            value={description}
            onChange={(e) => setDescription(e.target.value)}
          />
        </div>
        <button type="submit" className="btn btn-primary">Создать</button>
      </form>
    </div>
  );
}

export default CreateForm;

