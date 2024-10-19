using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace E3DSchedule
{
    static class E3DSchedule
    {
        /// <summary>
        /// The main entry point for the application.
        /// </summary>
        [STAThread]
        static void Main(string[] args)
        {
            string[] passArgs = new string[0];
            if ( args.Length > 0)
            {
                passArgs = args;
            }
            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);
            Application.Run(new FormE3DSchedule(passArgs));
        }
    }
}
