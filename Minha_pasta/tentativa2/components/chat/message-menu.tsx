"use client"

import { useState } from "react"
import { Edit2, Trash2, Trash } from "lucide-react"

interface MessageMenuProps {
  messageId: number
  sentTime: Date
  onEdit: () => void
  onDeleteForMe: () => void
  onDeleteForAll: () => void
  onClose: () => void
}

export default function MessageMenu({
  messageId,
  sentTime,
  onEdit,
  onDeleteForMe,
  onDeleteForAll,
  onClose,
}: MessageMenuProps) {
  const [showDeleteConfirm, setShowDeleteConfirm] = useState(false)

  const now = new Date()
  const timeElapsed = now.getTime() - sentTime.getTime()
  const fifteenMinutes = 15 * 60 * 1000
  const canEdit = timeElapsed < fifteenMinutes

  const handleDeleteForAll = () => {
    onDeleteForAll()
    setShowDeleteConfirm(false)
    onClose()
  }

  return (
    <>
      <div className="absolute top-0 right-0 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-lg shadow-lg z-50 min-w-48">
        <div className="flex flex-col">
          <button
            onClick={() => {
              onEdit()
              onClose()
            }}
            disabled={!canEdit}
            className={`flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-slate-600 ${
              !canEdit ? "opacity-50 cursor-not-allowed" : ""
            }`}
          >
            <Edit2 className="w-4 h-4" />
            Editar Mensagem
            {!canEdit && <span className="text-xs ml-auto opacity-60">(expirado)</span>}
          </button>

          <button
            onClick={() => {
              onDeleteForMe()
              onClose()
            }}
            className="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-slate-600"
          >
            <Trash2 className="w-4 h-4" />
            Excluir Para Mim
          </button>

          <button
            onClick={() => setShowDeleteConfirm(true)}
            className="flex items-center gap-2 px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-slate-600"
          >
            <Trash className="w-4 h-4" />
            Excluir Para Todos
          </button>
        </div>
      </div>

      {showDeleteConfirm && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 rounded-lg">
          <div className="bg-white dark:bg-slate-800 p-6 rounded-lg shadow-xl max-w-sm">
            <h3 className="text-lg font-bold mb-2 text-gray-900 dark:text-white">Excluir para todos?</h3>
            <p className="text-sm text-gray-600 dark:text-gray-300 mb-4">
              Tem certeza? Esta ação não pode ser revertida e a mensagem será deletada para todos os participantes.
            </p>
            <div className="flex gap-3 justify-end">
              <button
                onClick={() => setShowDeleteConfirm(false)}
                className="px-4 py-2 text-sm rounded-lg bg-gray-100 dark:bg-slate-700 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-slate-600"
              >
                Cancelar
              </button>
              <button
                onClick={handleDeleteForAll}
                className="px-4 py-2 text-sm rounded-lg bg-red-600 text-white hover:bg-red-700"
              >
                Deletar
              </button>
            </div>
          </div>
        </div>
      )}
    </>
  )
}
