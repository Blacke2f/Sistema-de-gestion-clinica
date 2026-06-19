using System;
using System.Diagnostics;
using System.IO;
using System.Threading;
using System.Runtime.InteropServices;

class Program
{
    [DllImport("kernel32.dll")]
    static extern IntPtr GetConsoleWindow();

    [DllImport("user32.dll")]
    static extern bool ShowWindow(IntPtr hWnd, int nCmdShow);

    const int SW_HIDE = 0;
    const int SW_SHOW = 5;

    static void Main(string[] args)
    {
        // Hide the console window initially
        var handle = GetConsoleWindow();
        ShowWindow(handle, SW_HIDE);

        string appDirectory = AppDomain.CurrentDomain.BaseDirectory;
        // Assuming the exe is in 'dist' and public is in '../public'
        string projectPath = Path.GetFullPath(Path.Combine(appDirectory, ".."));
        string publicPath = Path.Combine(projectPath, "public");
        string artisanPath = Path.Combine(projectPath, "artisan");

        if (!File.Exists(artisanPath))
        {
            // If executed directly from the root
            publicPath = Path.Combine(appDirectory, "public");
            artisanPath = Path.Combine(appDirectory, "artisan");
            projectPath = appDirectory;
        }

        // 1. Start PHP Built-in Server
        Process phpProcess = new Process();
        phpProcess.StartInfo.FileName = "php";
        phpProcess.StartInfo.Arguments = "artisan serve --port=8123";
        phpProcess.StartInfo.WorkingDirectory = projectPath;
        phpProcess.StartInfo.UseShellExecute = false;
        phpProcess.StartInfo.CreateNoWindow = true;
        phpProcess.Start();

        // Give it a second to start
        Thread.Sleep(1500);

        // 2. Open Default Browser
        Process browserProcess = new Process();
        browserProcess.StartInfo.FileName = "http://127.0.0.1:8123";
        browserProcess.StartInfo.UseShellExecute = true;
        browserProcess.Start();

        // 3. Show console and wait for user to exit
        ShowWindow(handle, SW_SHOW);
        Console.Title = "SISGECLIN - Servidor Interno";
        Console.WriteLine("=============================================");
        Console.WriteLine(" SISGECLIN se esta ejecutando en tu navegador");
        Console.WriteLine("=============================================");
        Console.WriteLine("");
        Console.WriteLine(" -> Ruta: http://127.0.0.1:8123");
        Console.WriteLine("");
        Console.WriteLine(" Por favor, NO cierres esta ventana mientras uses el sistema.");
        Console.WriteLine(" Para apagar el sistema, presiona ENTER aqui o cierra esta ventana.");
        Console.ReadLine();

        // 4. Kill PHP server when done
        try
        {
            if (!phpProcess.HasExited)
            {
                phpProcess.Kill();
            }
        }
        catch (Exception) { }
    }
}
