import React, { useState } from "react";
import axios from "axios";

function CreateForm() {
  const [title, setTitle] = useState("");
  const [description, setDescription] = useState("");
  const [isScorable, setIsScorable] = useState(false); // Новый state для оценки
  const [questions, setQuestions] = useState([]);

  const handleAddQuestion = () => {
    setQuestions([...questions, { text: "", type: "text", options: [], maxScore: 1 }]);
  };

  const handleQuestionChange = (index, field, value) => {
    const updatedQuestions = [...questions];
    updatedQuestions[index][field] = value;
    setQuestions(updatedQuestions);
  };

  const handleRemoveQuestion = (index) => {
    const updatedQuestions = [...questions];
    updatedQuestions.splice(index, 1);
    setQuestions(updatedQuestions);
  };

  const handleSubmit = async (event) => {
    event.preventDefault();

    try {
      const response = await axios.post("/api/forms", {
        title,
        description,
        isScorable, // Передаем флаг оценки
        questions
      });

      if (response.status === 200) {
        alert("Форма успешно создана!");
        window.location.href = "/forms";
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

        {/* Чекбокс для включения оценки */}
        <div className="form-check">
          <input
            type="checkbox"
            className="form-check-input"
            checked={isScorable}
            onChange={(e) => setIsScorable(e.target.checked)}
          />
          <label className="form-check-label">Включить возможность оценки</label>
        </div>

        <h4>Вопросы</h4>
        {questions.map((q, index) => (
          <div key={index} className="mb-3 p-3 border rounded">
            <input
              type="text"
              className="form-control mb-2"
              placeholder="Текст вопроса"
              value={q.text}
              onChange={(e) => handleQuestionChange(index, "text", e.target.value)}
              required
            />
            <select
              className="form-select mb-2"
              value={q.type}
              onChange={(e) => handleQuestionChange(index, "type", e.target.value)}
            >
              <option value="text">Текст</option>
              <option value="number">Число</option>
              <option value="radio">Один вариант</option>
              <option value="checkbox">Несколько вариантов</option>
              <option value="file_upload">Загрузка файла</option>
              <option value="rating">Оценка</option>
              <option value="scale">Шкала</option>
            </select>

            {(q.type === "radio" || q.type === "checkbox") && (
              <div>
                <h6>Варианты ответов</h6>
                {q.options.map((opt, i) => (
                  <input
                    key={i}
                    type="text"
                    className="form-control mb-1"
                    placeholder={`Вариант ${i + 1}`}
                    value={opt}
                    onChange={(e) => {
                      const updatedOptions = [...q.options];
                      updatedOptions[i] = e.target.value;
                      handleQuestionChange(index, "options", updatedOptions);
                    }}
                  />
                ))}
                <button
                  type="button"
                  className="btn btn-sm btn-outline-primary mt-1"
                  onClick={() => handleQuestionChange(index, "options", [...q.options, ""])}
                >
                  Добавить вариант
                </button>
              </div>
            )}

            {/* Если включено оценивание, показываем максимальный балл */}
            {isScorable && (
              <div className="mt-2">
                <label className="form-label">Максимальный балл за вопрос</label>
                <input
                  type="number"
                  className="form-control"
                  value={q.maxScore || 1}
                  min="1"
                  onChange={(e) => handleQuestionChange(index, "maxScore", e.target.value)}
                />
              </div>
            )}

            <button
              type="button"
              className="btn btn-sm btn-outline-danger mt-2"
              onClick={() => handleRemoveQuestion(index)}
            >
              Удалить
            </button>
          </div>
        ))}

        <button type="button" className="btn btn-secondary" onClick={handleAddQuestion}>
          Добавить вопрос
        </button>
        <button type="submit" className="btn btn-primary mt-3">Создать</button>
      </form>
    </div>
  );
}

export default CreateForm;
