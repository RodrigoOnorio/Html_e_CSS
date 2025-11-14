"use client"

import { useState } from "react"
import { X, Check } from "lucide-react"

interface EditMessageBoxProps {
  initialText: string
  onSave: (newText: string) => void
  onCancel: () => void
}

export default function EditMessageBox({ initialText, onSave, onCancel }: EditMessageBoxProps) {
  const [editText, setEditText] = useState(initialText)

  const handleSave = () => {
    if (editText.trim() && editText !== initialText) {
      onSave(editText)
    }
  }

  return (
    <div className="bg-blue-50 dark:bg-slate-700 border border-blue-200 dark:border-slate-600 rounded-lg p-3 mb-3 animate-in fade-in duration-200">
      <div className="flex gap-2 items-start">
        <div className="flex-1">
          <p className="text-xs font-semibold text-blue-700 dark:text-blue-300 mb-2">Editar mensagem</p>
          <input
            type="text"
            value={editText}
            onChange={(e) => setEditText(e.target.value)}
            className="w-full bg-white dark:bg-slate-800 border border-blue-200 dark:border-slate-600 rounded px-3 py-2 text-sm"
            autoFocus
          />
        </div>
        <div className="flex gap-2">
          <button
            onClick={handleSave}
            disabled={!editText.trim() || editText === initialText}
            className="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-slate-600 rounded disabled:opacity-50"
          >
            <Check className="w-4 h-4" />
          </button>
          <button onClick={onCancel} className="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-slate-600 rounded">
            <X className="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>
  )
}
