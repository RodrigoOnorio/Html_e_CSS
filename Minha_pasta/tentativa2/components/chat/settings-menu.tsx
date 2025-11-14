"use client"
import { Moon, Sun, LogOut, Trash2 } from "lucide-react"

interface SettingsMenuProps {
  isDark: boolean
  onToggleDark: () => void
  onLogout: () => void
  onClose: () => void
}

export default function SettingsMenu({ isDark, onToggleDark, onLogout, onClose }: SettingsMenuProps) {
  const handleClearMessages = () => {
    if (confirm("Tem certeza que deseja limpar todas as mensagens?")) {
      localStorage.removeItem("messages")
      window.location.reload()
    }
  }

  return (
    <div
      className={`absolute top-12 right-0 border rounded-lg shadow-lg min-w-max z-50 ${
        isDark ? "bg-slate-800 border-slate-700" : "bg-white border-gray-200"
      }`}
    >
      <div className="py-2">
        <button
          onClick={() => {
            onToggleDark()
            onClose()
          }}
          className={`w-full px-4 py-2 flex items-center gap-2 hover:bg-blue-100 dark:hover:bg-slate-700 transition-colors text-left ${
            isDark ? "text-white" : "text-gray-900"
          }`}
        >
          {isDark ? <Sun className="w-4 h-4" /> : <Moon className="w-4 h-4" />}
          <span className="text-sm">{isDark ? "Modo Claro" : "Modo Escuro"}</span>
        </button>

        <button
          onClick={handleClearMessages}
          className={`w-full px-4 py-2 flex items-center gap-2 hover:bg-red-100 dark:hover:bg-slate-700 transition-colors text-left text-red-600`}
        >
          <Trash2 className="w-4 h-4" />
          <span className="text-sm">Limpar Mensagens</span>
        </button>

        <div className={`border-t my-2 ${isDark ? "border-slate-700" : "border-gray-200"}`}></div>

        <button
          onClick={() => {
            onLogout()
            onClose()
          }}
          className={`w-full px-4 py-2 flex items-center gap-2 hover:bg-blue-100 dark:hover:bg-slate-700 transition-colors text-left ${
            isDark ? "text-white" : "text-gray-900"
          }`}
        >
          <LogOut className="w-4 h-4" />
          <span className="text-sm">Sair</span>
        </button>
      </div>
    </div>
  )
}
