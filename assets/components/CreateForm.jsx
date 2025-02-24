import React, { useState } from "react";
import axios from "axios";

function CreateForm() {
  const [title, setTitle] = useState("");
  const [description, setDescription] = useState("");
  const [questions, setQuestions] = useState([""]);

  const addQuestion = () => setQuestions([...questions, ""]);

  const handleSubmit = async () => {
    await axios.post("/api/forms", {
      title,
      description,
      questions
    });
    alert("Форма создана!");
  };

  return (
    <div>
      <h2>Создать форму</h2>
      <input placeholder="Название" onChange={(e) => setTitle(e.target.value)} />
      <textarea placeholder="Описание" onChange={(e) => setDescription(e.target.value)} />
      {questions.map((q, i) => (
        <input key={i} placeholder="Вопрос" onChange={(e) => {
          const updated = [...questions];
          updated[i] = e.target.value;
          setQuestions(updated);
        }} />
      ))}
      <button onClick={addQuestion}>Добавить вопрос</button>
      <button onClick={handleSubmit}>Создать</button>
    </div>
  );
}

export default CreateForm;
